<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest\Assets;

use MabeEnum\Enum;

class DeprecatedEnum extends Enum
{
    /**
     * @deprecated Test deprecated reflection
     */
    const DEPRECATED = 'deprecated';

    const NOT_DEPRECATED = 'not deprecated';
}
