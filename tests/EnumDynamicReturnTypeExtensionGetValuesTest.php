<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use MabeEnum\Enum;
use MabeEnum\EnumSet;
use MabeEnumPHPStan\EnumDynamicReturnTypeExtension;

final class EnumDynamicReturnTypeExtensionGetValuesTest extends ExtensionTestCase
{
    /** @var EnumDynamicReturnTypeExtension */
    private $extension;

    /** @var string */
    private $baseType = 'array<int, array|bool|float|int|string|null>';

    protected function setUp(): void
    {
        parent::setUp();

        if (!method_exists(Enum::class, 'getValues')) {
            $this->markTestSkipped('Enum::getValues() not supported in version 1.x');
        }

        $this->extension = new EnumDynamicReturnTypeExtension();

        // The base type has been set more specific in 4.3.0
        // The method EnumSet::__debugInfo also where added in 4.3.0
        if (!method_exists(EnumSet::class, '__debugInfo')) {
            $this->baseType = 'array';
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

        $this->processCode($code, '$enum->getValues()', 'array(null)', $this->extension);
    }

    public function testBoolType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\BoolTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValues()', 'array(true, false)', $this->extension);
    }

    public function testStringType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\StrTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValues()', "array('str1', 'str2')", $this->extension);
    }

    public function testIntType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\IntTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValues()', 'array(0, 1)', $this->extension);
    }

    public function testFloatType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\FloatTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValues()', 'array(1.1, 1.2)', $this->extension);
    }

    public function testArrayType(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnumPHPStanTest\Assets\ArrayTypeEnum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValues()', 'array(array(array()))', $this->extension);
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
            '$enum->getValues()',
            "array(null, true, 1, 1.1, 'str', array(null, true, 1, 1.1, 'str', array()))",
            $this->extension
        );
    }

    public function testBaseEnum(): void
    {
        $code = <<<'CODE'
<?php
function f(MabeEnum\Enum $enum) {
    die;
}
CODE;

        $this->processCode($code, '$enum->getValues()', $this->baseType, $this->extension);
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

        $this->processCode($code, '$enum->getValues()', "array(0|'str1', 1|'str2')", $this->extension);
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

        $this->processCode($code, '$enum->getValues()', 'array', $this->extension);
    }
}
