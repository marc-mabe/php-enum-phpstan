<?php declare(strict_types = 1);

namespace MabeEnum\PHPStan\tests\integration\data\EnumMethodsClassReflection;

use MabeEnum\Enum;

class Example
{
    public static function valid(): MyEnum
    {
        return MyEnum::ENUMERATOR();
    }

    public static function fail(): MyEnum
    {
        return MyEnum::NOT_AN_ENUMERATOR();
    }
}

class MyEnum extends Enum
{
    const ENUMERATOR = 'enumerator';
}
