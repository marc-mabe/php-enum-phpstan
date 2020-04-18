<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use MabeEnum\Enum;
use MabeEnumPHPStan\EnumDynamicReturnTypeExtension;

final class EnumDynamicReturnTypeExtensionTest extends ExtensionTestCase
{
    /** @var EnumDynamicReturnTypeExtension */
    private $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new EnumDynamicReturnTypeExtension();
    }

    public function testUnsupportedMethod(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\IntTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getName()', 'string', $this->extension);
    }
}
