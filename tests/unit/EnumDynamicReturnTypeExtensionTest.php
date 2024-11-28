<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\unit;

use MabeEnum\Enum;
use MabeEnumPHPStan\EnumDynamicReturnTypeExtension;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Testing\PHPStanTestCase;

class EnumDynamicReturnTypeExtensionTest extends PHPStanTestCase
{
    protected EnumDynamicReturnTypeExtension $extension;

    public function setUp(): void
    {
        $this->extension = new EnumDynamicReturnTypeExtension();
    }

    public function testGetClass(): void
    {
        $this->assertSame(Enum::class, $this->extension->getClass());
    }

    private function createMethodWithName(string $name): MethodReflection
    {
        $method = $this->createMock(MethodReflection::class);
        $method->method('getName')->willReturn($name);

        return $method;
    }

    /** @dataProvider staticMethodsProvider */
    public function testIsStaticMethodSupportedShouldReturnTrue(string $method): void
    {
        $reflectionMethod = $this->createMethodWithName($method);
        $this->assertTrue($this->extension->isStaticMethodSupported($reflectionMethod));

        $reflectionMethod = $this->createMethodWithName(strtolower($method));
        $this->assertTrue($this->extension->isStaticMethodSupported($reflectionMethod));

        $reflectionMethod = $this->createMethodWithName(strtoupper($method));
        $this->assertTrue($this->extension->isStaticMethodSupported($reflectionMethod));
    }

    public function testIsStaticMethodSupportedShouldReturnFalse(): void
    {
        $reflectionMethod = $this->createMethodWithName('fooBar');
        $this->assertFalse($this->extension->isStaticMethodSupported($reflectionMethod));
    }

    /** @dataProvider objectMethodsProvider */
    public function testIsMethodSupportedShouldReturnTrue(string $method): void
    {
        $reflectionMethod = $this->createMethodWithName($method);
        $this->assertTrue($this->extension->isMethodSupported($reflectionMethod));

        $reflectionMethod = $this->createMethodWithName(strtolower($method));
        $this->assertTrue($this->extension->isMethodSupported($reflectionMethod));

        $reflectionMethod = $this->createMethodWithName(strtoupper($method));
        $this->assertTrue($this->extension->isMethodSupported($reflectionMethod));
    }

    public function testIsMethodSupportedShouldReturnFalse(): void
    {
        $reflectionMethod = $this->createMethodWithName('fooBar');
        $this->assertFalse($this->extension->isMethodSupported($reflectionMethod));
    }

    public function staticMethodsProvider(): array
    {
        return [['getValues']];
    }

    public function objectMethodsProvider(): array
    {
        return array_merge([
            ['getValue'],
        ], $this->staticMethodsProvider());
    }
}
