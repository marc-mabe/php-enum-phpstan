<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use MabeEnumPHPStan\EnumMethodReflection;
use MabeEnumPHPStan\EnumMethodsClassReflectionExtension;
use MabeEnumPHPStanTest\Assets\NotAnEnum;
use MabeEnumPHPStanTest\Assets\StrEnum;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Testing\TestCase;
use PHPStan\Type\VerbosityLevel;

class EnumMethodsClassReflectionExtensionTest extends TestCase
{
    /**
     * @var \PHPStan\Broker\Broker
     */
    protected $broker;

    /**
     * @var EnumMethodsClassReflectionExtension
     */
    protected $reflectionExtension;

    public function setUp()
    {
        $this->broker = $this->createBroker();
        $this->reflectionExtension = new EnumMethodsClassReflectionExtension();
    }

    public function testHasMethodSuccess()
    {
        $classReflection = $this->broker->getClass(StrEnum::class);

        foreach (StrEnum::getNames() as $name) {
            $this->assertTrue($this->reflectionExtension->hasMethod($classReflection, $name));
        }
    }

    public function testHasMethodPrivateProtectedNotFound()
    {
        $classReflection = $this->broker->getClass(StrEnum::class);
        $this->assertFalse($this->reflectionExtension->hasMethod($classReflection, 'PRIVATE_STR'));
        $this->assertFalse($this->reflectionExtension->hasMethod($classReflection, 'PROTECTED_STR'));
    }

    public function testHasMethodUnknownNotFound()
    {
        $classReflection = $this->broker->getClass(StrEnum::class);
        $this->assertFalse($this->reflectionExtension->hasMethod($classReflection, 'UNKNOWN'));
    }

    public function testHasMethodNotSubclassOfEnumNotFound()
    {
        $classReflection = $this->broker->getClass(NotAnEnum::class);
        $this->assertFalse($this->reflectionExtension->hasMethod($classReflection, 'STR'));
    }

    public function testGetMethodSuccess()
    {
        $classReflection = $this->broker->getClass(StrEnum::class);

        foreach (StrEnum::getNames() as $name) {
            $methodReflection = $this->reflectionExtension->getMethod($classReflection, $name);

            $this->assertInstanceOf(EnumMethodReflection::class, $methodReflection);
            $this->assertSame($classReflection, $methodReflection->getDeclaringClass());
        }
    }
}
