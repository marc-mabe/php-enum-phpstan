<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use MabeEnum\Enum;
use MabeEnumPHPStan\EnumDynamicReturnTypeExtension;

final class EnumDynamicReturnTypeExtensionGetValueTest extends ExtensionTestCase
{
    /** @var EnumDynamicReturnTypeExtension */
    private $extension;

    private $defaultReturnType = 'array|bool|float|int|string|null';

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new EnumDynamicReturnTypeExtension();

        // Version < 3.x did not support array values
        if (method_exists(Enum::class, 'getByName')) {
            $this->defaultReturnType = 'bool|float|int|string|null';
        }
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
    }

    public function testAllTypes(): void
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
    }

    public function testBaseEnum(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnum\Enum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', $this->defaultReturnType, $this->extension);
    }

    public function testUnionEnum(): void
    {
        $code = <<<'CODE'
<?php
use MabeEnumPHPStanTest\Assets\IntTypeEnum;
use MabeEnumPHPStanTest\Assets\StrTypeEnum;

/** @param IntTypeEnum|StrTypeEnum $enum */
function f($enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', "0|1|'str1'|'str2'", $this->extension);
    }

    public function testEnumAndNonEnum(): void
    {
        $code = <<<'CODE'
<?php
use MabeEnumPHPStanTest\Assets\IntTypeEnum;
use MabeEnumPHPStanTest\Assets\NotAnEnum;

/** @param IntTypeEnum|NotAnEnum $enum */
function f($enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValue()', $this->defaultReturnType, $this->extension);
    }
}
