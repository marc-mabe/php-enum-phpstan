<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest\Assets;

use MabeEnum\Enum;

class AllTypeEnum extends Enum
{
    const NIL = null;
    const BOOL = true;
    const INT = 1;
    const FLOAT = 1.1;
    const STR = 'str';
    const ARR = [null, true, 1, 1.1, 'str', []];
}
