<?php declare(strict_types = 1);

namespace MabeEnum\PHPStan\tests\integration;

use MabeEnum\Enum;
use PHPStan\Testing\LevelsTestCase;

final class IntegrationTest extends LevelsTestCase
{

    /**
     * @return string[][]
     */
    public function dataTopics(): array
    {
        $dataTopics = [
            ['EnumMethodsClassReflection'],
            ['EnumGetValueReturnType'],
        ];

        if (method_exists(Enum::class, 'getValues')) {
            $dataTopics[] = ['EnumGetValuesReturnType'];
        }

        return $dataTopics;
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
