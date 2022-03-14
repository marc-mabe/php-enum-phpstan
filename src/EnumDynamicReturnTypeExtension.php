<?php declare(strict_types=1);

namespace MabeEnumPHPStan;

use MabeEnum\Enum;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantArrayTypeBuilder;
use PHPStan\Type\ConstantTypeHelper;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class EnumDynamicReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension, DynamicMethodReturnTypeExtension
{
    /**
     * Map supported object method to a callable function detecting return type
     *
     * @var array<string, callable>
     */
    private $objectMethods = [];

    /**
     * Map supported static method to a callable function detecting return type
     *
     * @var array<string, callable>
     */
    private $staticMethods = [];

    /**
     * Buffer of all types of enumeration values
     * @phpstan-var array<class-string<Enum>, Type[]>
     */
    private $enumValueTypesBuffer = [];

    /**
     * Buffer of all types of enumeration ordinals
     * @phpstan-var array<class-string<Enum>, Type[]>
     */
    private $enumOrdinalTypesBuffer = [];

    public function __construct()
    {
        $this->objectMethods['getvalue'] = function (string $class) {
            return $this->detectGetValueReturnType($class);
        };

        if (method_exists(Enum::class, 'getvalues')) {
            $this->staticMethods['getvalues'] = function (string $class) {
                return $this->detectGetValuesReturnType($class);
            };
        }

        // static methods cann be called like object methods
        $this->objectMethods = array_merge($this->objectMethods, $this->staticMethods);
    }

    public function getClass(): string
    {
        return Enum::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        $methodLower = strtolower($methodReflection->getName());
        return array_key_exists($methodLower, $this->staticMethods);
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $methodLower = strtolower($methodReflection->getName());
        return array_key_exists($methodLower, $this->objectMethods);
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $staticCall,
        Scope $scope
    ): Type {
        if ($staticCall->class instanceof Expr) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }
        $callClass = $staticCall->class->toString();

        // Can't detect possible types on static::*()
        // as it depends on defined enumerators of unknown inherited classes
        if ($callClass === 'static') {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }

        if ($callClass === 'self') {
            $callClass = $scope->getClassReflection()->getName();
        }

        $methodLower = strtolower($methodReflection->getName());
        return $this->objectMethods[$methodLower]($callClass);
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $methodLower = strtolower($methodReflection->getName());
        $returnTypes = [];
        foreach ($scope->getType($methodCall->var)->getReferencedClasses() as $callClass) {
            $returnTypes[] = $this->objectMethods[$methodLower]($callClass);
        }

        return TypeCombinator::union(...$returnTypes);
    }

    /**
     * Returns types of all values of an enumeration
     * @phpstan-param class-string<Enum> $enumeration
     * @return Type[]
     */
    private function enumValueTypes(string $enumeration): array
    {
        if (isset($this->enumValueTypesBuffer[$enumeration])) {
            return $this->enumValueTypesBuffer[$enumeration];
        }

        $values = array_values($enumeration::getConstants());
        $types  = array_map([ConstantTypeHelper::class, 'getTypeFromValue'], $values);

        return $this->enumValueTypesBuffer[$enumeration] = $types;
    }

    /**
     * Returns types of all ordinals of an enumeration
     * @phpstan-param class-string<Enum> $enumeration
     * @return Type[]
     */
    private function enumOrdinalTypes(string $enumeration): array
    {
        if (isset($this->enumOrdinalTypesBuffer[$enumeration])) {
            return $this->enumOrdinalTypesBuffer[$enumeration];
        }

        $ordinals = array_keys($enumeration::getOrdinals());
        $types    = array_map([ConstantTypeHelper::class, 'getTypeFromValue'], $ordinals);

        return $this->enumOrdinalTypesBuffer[$enumeration] = $types;
    }

    /**
     * Returns return type of Enum::getValue()
     * @phpstan-param class-string<Enum> $enumeration
     */
    private function detectGetValueReturnType(string $enumeration): Type
    {
        return TypeCombinator::union(...$this->enumValueTypes($enumeration));
    }

    /**
     * Returns return type of Enum::getValues()
     * @phpstan-param class-string<Enum> $enumeration
     */
    private function detectGetValuesReturnType(string $enumeration): Type
    {
        $keyTypes   = $this->enumOrdinalTypes($enumeration);
        $valueTypes = $this->enumValueTypes($enumeration);

        $builder = ConstantArrayTypeBuilder::createEmpty();
        foreach ($keyTypes as $i => $keyType) {
            $valueType = $valueTypes[$i];
            $builder->setOffsetValueType($keyType, $valueType);
        }

        return $builder->getArray();
    }
}
