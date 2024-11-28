<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\integration;

use PHPStan\Testing\LevelsTestCase;

final class IntegrationTest extends LevelsTestCase
{
    /**
     * @return string[][]
     */
    public static function dataTopics(): array
    {
        return [
            ['EnumMethodsClassReflection'],
            ['EnumGetValueReturnType'],
            ['EnumGetValuesReturnType'],
        ];
    }

    public function getDataPath(): string
    {
        return __DIR__ . '/data';
    }

    public function getPhpStanExecutablePath(): string
    {
        return __DIR__ . '/../../vendor/phpstan/phpstan/phpstan';
    }

    public function getPhpStanConfigPath(): string
    {
        return __DIR__ . '/phpstan.neon';
    }
}
