<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\assets;

use MabeEnum\Enum;

class DeprecatedEnum extends Enum
{
    /**
     * @deprecated Test deprecated reflection
     */
    const DEPRECATED = 'deprecated';

    const NOT_DEPRECATED = 'not deprecated';
}
