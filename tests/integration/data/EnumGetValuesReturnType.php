<?php declare(strict_types = 1);

namespace MabeEnum\PHPStan\tests\integration\data\EnumGetValuesReturnType;

use MabeEnum\Enum;

class Example
{
    /** @return array<int, null> */
    public static function baseMethodValid(): array
    {
        return Enum::getValues();
    }

    /** @return array<int, float|int|string> */
    public static function staticMethodValid(): array
    {
        return MyInheritedEnum::getValues();
    }

    /** @return array<int, null> */
    public static function staticMethodFail(): array
    {
        return MyInheritedEnum::getValues();
    }

    /** @return array<int, float|int|string> */
    public static function objectMethodValid(): array
    {
        return MyInheritedEnum::STR()->getValues();
    }

    /** @return array<int, null> */
    public static function objectMethodFail(): array
    {
        return MyInheritedEnum::STR()->getValues();
    }
}

class MyEnum extends Enum
{
    const STR = 'str';
    const INT = 1;

    /** @return array<int, int|string> */
    public static function selfGetValuesValid(): array
    {
        return self::getValues();
    }

    /** @return array<int, null> */
    public static function selfGetValuesFail(): array
    {
        return self::getValues();
    }

    /** @return array<int, null> */
    public static function staticGetValuesFail(): array
    {
        return static::getValues();
    }
}

class MyInheritedEnum extends MyEnum
{
    const FLOAT = 1.1;

    /** @return array<int, float|int|string> */
    public static function inheritSelfGetValuesValid(): array
    {
        return self::getValues();
    }

    /** @return array<int, null> */
    public static function inheritSelfGetValuesFail(): array
    {
        return self::getValues();
    }

    /** @return array<int, null> */
    public static function inheritStaticGetValuesFail(): array
    {
        return static::getValues();
    }
}
