<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\assets;

class NotAnEnum
{
    const STR = 'str';
    private const PRIVATE_STR = 'private str';
    protected const PROTECTED_STR = 'protected str';
    public const PUBLIC_STR = 'public str';

    public function getValue(): string {return __FUNCTION__; }
    public function getValues(): array { return []; }
}
