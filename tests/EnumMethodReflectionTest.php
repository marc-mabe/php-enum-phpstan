<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use MabeEnumPHPStan\EnumMethodReflection;
use MabeEnumPHPStan\EnumMethodsClassReflectionExtension;
use MabeEnumPHPStanTest\Assets\DeprecatedEnum;
use MabeEnumPHPStanTest\Assets\DocCommentEnum;
use MabeEnumPHPStanTest\Assets\NotAnEnum;
use MabeEnumPHPStanTest\Assets\VisibilityEnum;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Testing\TestCase;
use PHPStan\TrinaryLogic;
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
        $classReflection  = $this->broker->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertSame($classReflection, $methodReflection->getDeclaringClass());
    }

    public function testShouldBeStatic()
    {
        $classReflection  = $this->broker->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isStatic());
    }

    public function testShouldNotBePrivate()
    {
        $classReflection  = $this->broker->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertFalse($methodReflection->isPrivate());
    }

    public function testShouldBePublic()
    {
        $classReflection  = $this->broker->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isPublic());
    }

    public function testGetVariants()
    {
        $classReflection  = $this->broker->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');
        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());

        $this->assertSame(VisibilityEnum::class, $parametersAcceptor->getReturnType()->describe(VerbosityLevel::value()));
    }

    public function testGetDocComment()
    {
        $classReflection = $this->broker->getClass(DocCommentEnum::class);
        $docMethodRefl = $this->reflectionExtension->getMethod($classReflection, 'WITH_DOC_BLOCK');
        $noDocMethodRefl = $this->reflectionExtension->getMethod($classReflection, 'WITHOUT_DOC_BLOCK');

        // return null on no doc block
        $this->assertSame(null, $noDocMethodRefl->getDocComment());

        // return the correct doc block
        $this->assertRegExp('/With doc block/', $docMethodRefl->getDocComment());

        // remove @var declaration
        $this->assertNotRegExp('/@var/', $docMethodRefl->getDocComment());
    }

    public function testIsDeprecated()
    {
        $classReflection = $this->broker->getClass(DeprecatedEnum::class);
        $deprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'DEPRECATED');
        $notDeprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'NOT_DEPRECATED');

        $this->assertTrue($deprecatedRefl->isDeprecated()->yes());
        $this->assertTrue($notDeprecatedRefl->isDeprecated()->no());
    }

    public function testGetDeprecatedDescription()
    {
        $classReflection = $this->broker->getClass(DeprecatedEnum::class);
        $deprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'DEPRECATED');
        $notDeprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'NOT_DEPRECATED');

        $this->assertSame('Test deprecated reflection', $deprecatedRefl->getDeprecatedDescription());
        $this->assertNull($notDeprecatedRefl->getDeprecatedDescription());
    }
}
