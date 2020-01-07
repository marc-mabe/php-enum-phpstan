<?php

declare(strict_types=1);

namespace MabeEnumPHPStan;

use MabeEnum\Enum;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;

class EnumMethodsClassReflectionExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if ($classReflection->isSubclassOf(Enum::class)) {
            $array = $classReflection->getNativeReflection()->getMethod('getConstants')->invoke(null);

            return array_key_exists($methodName, $array);
        }

        return false;
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return new EnumMethodReflection($classReflection, $methodName);
    }
}
