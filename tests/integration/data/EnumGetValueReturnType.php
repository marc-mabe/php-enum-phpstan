<?php declare(strict_types = 1);

namespace MabeEnum\PHPStan\tests\integration\data\EnumGetValueReturnType;

use MabeEnum\Enum;

class Example
{
    /** @return float|int|string */
    public static function valid()
    {
        return MyEnum::STR()->getValue();
    }

    /** @return bool */
    public static function fail()
    {
        return MyEnum::STR()->getValue();
    }
}

class MyEnum extends Enum
{
    const STR = 'str';
    const INT = 1;
    const FLOAT = 1.1;
}
