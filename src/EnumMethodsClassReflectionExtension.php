<?php declare(strict_types=1);

namespace MabeEnumPHPStan;

use MabeEnum\Enum;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

class EnumMethodsClassReflectionExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!$classReflection->isSubclassOf(Enum::class)) {
            return false;
        }

        $class = $classReflection->getName();
        return array_key_exists($methodName, $class::getConstants());
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new EnumMethodReflection($classReflection, $methodName);
    }
}
