<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\unit;

use MabeEnumPHPStan\EnumMethodReflection;
use MabeEnumPHPStan\EnumMethodsClassReflectionExtension;
use MabeEnum\PHPStan\tests\assets\NotAnEnum;
use MabeEnum\PHPStan\tests\assets\VisibilityEnum;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;

class EnumMethodsClassReflectionExtensionTest extends PHPStanTestCase
{
    protected ReflectionProvider $reflectionProvider;
    protected EnumMethodsClassReflectionExtension $extension;

    public function setUp(): void
    {
        $this->reflectionProvider = $this->createReflectionProvider();
        $this->extension = new EnumMethodsClassReflectionExtension();
    }

    public function testHasMethodSuccess(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);

        foreach (array_keys(VisibilityEnum::getConstants()) as $name) {
            $this->assertTrue($this->extension->hasMethod($classReflection, $name));
        }
    }

    public function testHasMethodUnknownNotFound(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $this->assertFalse($this->extension->hasMethod($classReflection, 'UNKNOWN'));
    }

    public function testHasMethodNotSubclassOfEnumNotFound(): void
    {
        $classReflection = $this->reflectionProvider->getClass(NotAnEnum::class);
        $this->assertFalse($this->extension->hasMethod($classReflection, 'STR'));
    }

    public function testGetMethodSuccess(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);

        foreach (array_keys(VisibilityEnum::getConstants()) as $name) {
            $methodReflection = $this->extension->getMethod($classReflection, $name);

            $this->assertInstanceOf(EnumMethodReflection::class, $methodReflection);
            $this->assertSame($classReflection, $methodReflection->getDeclaringClass());
        }
    }
}
