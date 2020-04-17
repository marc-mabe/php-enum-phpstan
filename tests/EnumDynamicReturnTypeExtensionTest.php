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

    public function testNullType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\NullTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', 'null', $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', 'array<int, null>', $this->extension);
        }
    }

    public function testBoolType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\BoolTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', 'bool', $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', 'array<int, bool>', $this->extension);
        }
    }

    public function testStringType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\StrTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', "'str1'|'str2'", $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', "array<int, 'str1'|'str2'>", $this->extension);
        }
    }

    public function testIntType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\IntTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', '0|1', $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', 'array<int, 0|1>', $this->extension);
        }
    }

    public function testFloatType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\FloatTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', '1.1|1.2', $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', 'array<int, 1.1|1.2>', $this->extension);
        }
    }

    public function testArrayType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\ArrayTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', 'array(array())', $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', 'array<int, array(array())>', $this->extension);
        }
    }

    public function testUnionTypes(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\AllTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode(
            $code,
            '$enum->getValue()',
            "1|1.1|'str'|array(null, true, 1, 1.1, 'str', array())|true|null",
            $this->extension
        );

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode(
                $code,
                '$enum->getValues()',
                "array<int, 1|1.1|'str'|array(null, true, 1, 1.1, 'str', array())|true|null>",
                $this->extension
            );
        }
    }

    public function testGeneralizedTypes(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\BigStrEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', 'string', $this->extension);

        if (method_exists(Enum::class, 'getValues')) {
            $this->processCode($code, '$enum->getValues()', 'array<int, string>', $this->extension);
        }
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
