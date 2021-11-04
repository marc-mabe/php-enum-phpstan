<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\unit;

use MabeEnumPHPStan\EnumMethodReflection;
use MabeEnumPHPStan\EnumMethodsClassReflectionExtension;
use MabeEnum\PHPStan\tests\assets\NotAnEnum;
use MabeEnum\PHPStan\tests\assets\VisibilityEnum;
use PHPStan\Testing\PHPStanTestCase;

class EnumMethodsClassReflectionExtensionTest extends PHPStanTestCase
{
    /**
     * @var \PHPStan\Broker\Broker
     */
    protected $broker;

    /**
     * @var EnumMethodsClassReflectionExtension
     */
    protected $extension;

    public function setUp(): void
    {
        $this->broker = $this->createBroker();
        $this->extension = new EnumMethodsClassReflectionExtension();
    }

    public function testHasMethodSuccess(): void
    {
        $classReflection = $this->broker->getClass(VisibilityEnum::class);

        foreach (array_keys(VisibilityEnum::getConstants()) as $name) {
            $this->assertTrue($this->extension->hasMethod($classReflection, $name));
        }
    }

    public function testHasMethodUnknownNotFound(): void
    {
        $classReflection = $this->broker->getClass(VisibilityEnum::class);
        $this->assertFalse($this->extension->hasMethod($classReflection, 'UNKNOWN'));
    }

    public function testHasMethodNotSubclassOfEnumNotFound(): void
    {
        $classReflection = $this->broker->getClass(NotAnEnum::class);
        $this->assertFalse($this->extension->hasMethod($classReflection, 'STR'));
    }

    public function testGetMethodSuccess(): void
    {
        $classReflection = $this->broker->getClass(VisibilityEnum::class);

        foreach (array_keys(VisibilityEnum::getConstants()) as $name) {
            $methodReflection = $this->extension->getMethod($classReflection, $name);

            $this->assertInstanceOf(EnumMethodReflection::class, $methodReflection);
            $this->assertSame($classReflection, $methodReflection->getDeclaringClass());
        }
    }
}
