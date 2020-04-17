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
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\IntegerType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class EnumDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * Buffer of known return types of Enum::getValues()
     * @var Type[]
     * @phpstan-var array<class-string<Enum>, Type>
     */
    private $enumValuesTypeBuffer = [];

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

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $enumType   = $scope->getType($methodCall->var);
        $methodName    = $methodReflection->getName();
        $methodClasses = $enumType->getReferencedClasses();
        if (count($methodClasses) !== 1) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }

        $enumeration = $methodClasses[0];

        switch (strtolower($methodName)) {
            case 'getvalue':
                return $this->getEnumValuesType($enumeration, $scope);
            case 'getvalues':
                return new ArrayType(
                    new IntegerType(),
                    $this->getEnumValuesType($enumeration, $scope)
                );
            default:
                throw new ShouldNotHappenException("Method {$methodName} is not supported");
        }
    }

    /**
     * Returns union type of all values of an enumeration
     * @phpstan-param class-string<Enum> $enumClass
     */
    private function getEnumValuesType(string $enumeration, Scope $scope): Type
    {
        if (isset($this->enumValuesTypeBuffer[$enumeration])) {
            return $this->enumValuesTypeBuffer[$enumeration];
        }

        $values = array_values($enumeration::getConstants());
        $types  = array_map(function ($value) use ($scope): Type {
            return $scope->getTypeFromValue($value);
        }, $values);

        return $this->enumValuesTypeBuffer[$enumeration] = TypeCombinator::union(...$types);
    }
}
