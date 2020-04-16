<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use MabeEnum\Enum;
use MabeEnumPHPStan\EnumGetValueDynamicReturnTypeExtension;

final class EnumGetValueDynamicReturnTypeExtensionTest extends ExtensionTestCase
{
    /**
     * @var \PHPStan\Broker\Broker
     */
    protected $broker;

    protected function setUp(): void
    {
        parent::setUp();

        if (!method_exists(Enum::class, 'getValues')) {
            self::markTestSkipped('Version 1.x is not supported.');
        }
    }

    public function testConstantTypes(): void
    {
        $this->processFile(__DIR__ . '/data/get_value.php', '$strEnum->getValue()', "'no doc block'|'public str'|'str'", new EnumGetValueDynamicReturnTypeExtension());
    }

    public function testGeneralizedTypes(): void
    {
        $this->processFile(__DIR__ . '/data/get_value.php', '$bigStrEnum->getValue()', "string", new EnumGetValueDynamicReturnTypeExtension());
    }
}
