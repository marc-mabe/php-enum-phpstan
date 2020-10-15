<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\unit;

use MabeEnum\Enum;
use MabeEnumPHPStan\EnumDynamicReturnTypeExtension;
use MabeEnumPHPStan\EnumMethodsClassReflectionExtension;
use PHPStan\Reflection\Dummy\DummyMethodReflection;
use PHPStan\Testing\TestCase;

class EnumDynamicReturnTypeExtensionTest extends TestCase
{
    /**
     * @var EnumMethodsClassReflectionExtension
     */
    protected $extension;

    public function setUp(): void
    {
        $this->extension = new EnumDynamicReturnTypeExtension();
    }

    public function testGetClass(): void
    {
        $this->assertSame(Enum::class, $this->extension->getClass());
    }

    /** @dataProvider staticMethodsProvider */
    public function testIsStaticMethodSupportedShouldReturnTrue(string $method): void
    {
        $reflectionMethod = new DummyMethodReflection($method);
        $this->assertTrue($this->extension->isStaticMethodSupported($reflectionMethod));

        $reflectionMethod = new DummyMethodReflection(strtolower($method));
        $this->assertTrue($this->extension->isStaticMethodSupported($reflectionMethod));

        $reflectionMethod = new DummyMethodReflection(strtoupper($method));
        $this->assertTrue($this->extension->isStaticMethodSupported($reflectionMethod));
    }

    public function testIsStaticMethodSupportedShouldReturnFalse(): void
    {
        $reflectionMethod = new DummyMethodReflection('fooBar');
        $this->assertFalse($this->extension->isStaticMethodSupported($reflectionMethod));
    }

    /** @dataProvider objectMethodsProvider */
    public function testIsMethodSupportedShouldReturnTrue(string $method): void
    {
        $reflectionMethod = new DummyMethodReflection($method);
        $this->assertTrue($this->extension->isMethodSupported($reflectionMethod));

        $reflectionMethod = new DummyMethodReflection(strtolower($method));
        $this->assertTrue($this->extension->isMethodSupported($reflectionMethod));

        $reflectionMethod = new DummyMethodReflection(strtoupper($method));
        $this->assertTrue($this->extension->isMethodSupported($reflectionMethod));
    }

    public function testIsMethodSupportedShouldReturnFalse(): void
    {
        $reflectionMethod = new DummyMethodReflection('fooBar');
        $this->assertFalse($this->extension->isMethodSupported($reflectionMethod));
    }

    public function staticMethodsProvider(): array
    {
        $staticMethods = [];

        if (method_exists(Enum::class, 'getValues')) {
            $staticMethods[] = ['getValues'];
        }

        return $staticMethods;
    }

    public function objectMethodsProvider(): array
    {
        return array_merge([
            ['getValue'],
        ], $this->staticMethodsProvider());
    }
}
