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

class EnumMethodReflectionTest extends TestCase
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

    public function getDeclaringClass()
    {
        $classReflection  = $this->broker->getClass(StrEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertSame($classReflection, $methodReflection->getDeclaringClass());
    }

    public function testShouldBeStatic()
    {
        $classReflection  = $this->broker->getClass(StrEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isStatic());
    }

    public function testShouldNotBePrivate()
    {
        $classReflection  = $this->broker->getClass(StrEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertFalse($methodReflection->isPrivate());
    }

    public function testShouldBePublic()
    {
        $classReflection  = $this->broker->getClass(StrEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isPublic());
    }

    public function testGetVariants()
    {
        $classReflection  = $this->broker->getClass(StrEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');
        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());

        $this->assertSame(StrEnum::class, $parametersAcceptor->getReturnType()->describe(VerbosityLevel::value()));
    }
}
