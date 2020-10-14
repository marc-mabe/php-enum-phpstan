<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\assets_;

use MabeEnum\Enum;

class VisibilityEnum extends Enum
{
    /**
     * String const without visibility declaration
     *
     * @var string
     */
    const STR = 'str';

    /**
     * Private string const
     */
    private const PRIVATE_STR = 'private str';

    /**
     * Protected string const
     */
    protected const PROTECTED_STR = 'protected str';

    /**
     * Public string const
     */
    public const PUBLIC_STR = 'public str';
}
