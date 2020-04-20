<?php

declare(strict_types=1);

namespace MabeEnumPHPStan;

use MabeEnum\Enum;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\ConstantTypeHelper;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class EnumDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
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

    public function getClass(): string
    {
        return Enum::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        $supportedMethods = ['getvalue'];
        if (method_exists(Enum::class, 'getValues')) {
            array_push($supportedMethods, 'getvalues');
        }

        return in_array(strtolower($methodReflection->getName()), $supportedMethods, true);
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $callType    = $scope->getType($methodCall->var);
        $callClasses = $callType->getReferencedClasses();
        $methodName  = strtolower($methodReflection->getName());
        $returnTypes = [];
        foreach ($callClasses as $callClass) {
            if (!is_subclass_of($callClass, Enum::class, true)) {
                $returnTypes[] = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())
                    ->getReturnType();
            } else {
                switch ($methodName) {
                    case 'getvalue':
                        $returnTypes[] = $this->enumGetValueReturnType($callClass);
                        break;
                    case 'getvalues':
                        $returnTypes[] = $this->enumGetValuesReturnType($callClass);
                        break;
                    default:
                        throw new ShouldNotHappenException("Method {$methodName} is not supported");
                }
            }
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
    private function enumGetValueReturnType(string $enumeration): Type
    {
        return TypeCombinator::union(...$this->enumValueTypes($enumeration));
    }

    /**
     * Returns return type of Enum::getValues()
     * @phpstan-param class-string<Enum> $enumeration
     */
    private function enumGetValuesReturnType(string $enumeration): ArrayType
    {
        $keyTypes   = $this->enumOrdinalTypes($enumeration);
        $valueTypes = $this->enumValueTypes($enumeration);
        return new ConstantArrayType($keyTypes, $valueTypes, count($keyTypes));
    }
}
