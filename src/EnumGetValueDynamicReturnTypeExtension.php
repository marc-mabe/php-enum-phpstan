<?php

declare(strict_types=1);

namespace MabeEnumPHPStan;

use MabeEnum\Enum;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\BooleanType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;
use PHPStan\Type\VerbosityLevel;

class EnumGetValueDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /** @var \PHPStan\Type\Type[] */
    private $enumTypes = [];

    public function getClass(): string
    {
        return Enum::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        // version 1.x has visibility bug in getConstants()
        return method_exists(Enum::class, 'getValues') && $methodReflection->getName() === 'getValue';
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): Type
    {
        $enumType = $scope->getType($methodCall->var);
        if (count($enumType->getReferencedClasses()) !== 1) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }

        /** @var string $enumClass */
        $enumClass = $enumType->getReferencedClasses()[0];
        if (array_key_exists($enumClass, $this->enumTypes)) {
            return $this->enumTypes[$enumClass];
        }
        $types = array_map(function ($value) use ($scope): Type {
            return $scope->getTypeFromValue($value);
        }, self::getEnumValues($enumClass));

        $this->enumTypes[$enumClass] = TypeCombinator::union(...$types);

        return $this->enumTypes[$enumClass];
    }

    /**
     * @phpstan-param class-string<Enum> $enumClass
     */
    private static function getEnumValues(string $enumClass): array
    {
        if (method_exists($enumClass, 'getValues')) {
            return $enumClass::getValues();
        }

        throw new \PHPStan\ShouldNotHappenException();
    }
}
