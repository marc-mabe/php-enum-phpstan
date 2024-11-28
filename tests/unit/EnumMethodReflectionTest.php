<?php declare(strict_types=1);

namespace MabeEnum\PHPStan\tests\unit;

use MabeEnumPHPStan\EnumMethodsClassReflectionExtension;
use MabeEnum\PHPStan\tests\assets\DeprecatedEnum;
use MabeEnum\PHPStan\tests\assets\DocCommentEnum;
use MabeEnum\PHPStan\tests\assets\VisibilityEnum;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Testing\PHPStanTestCase;
use PHPStan\Type\VerbosityLevel;
use PHPStan\Analyser\Scope;

class EnumMethodReflectionTest extends PHPStanTestCase
{
    protected ReflectionProvider $reflectionProvider;
    protected EnumMethodsClassReflectionExtension $reflectionExtension;

    public function setUp(): void
    {
        $this->reflectionProvider = $this->createReflectionProvider();
        $this->reflectionExtension = new EnumMethodsClassReflectionExtension();
    }

    public function testGetName(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertSame('STR', $methodReflection->getName());
    }

    public function testGetDeclaringClass(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertSame($classReflection, $methodReflection->getDeclaringClass());
    }

    public function testShouldBeStatic(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isStatic());
    }

    public function testShouldNotBePrivate(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertFalse($methodReflection->isPrivate());
    }

    public function testShouldBePublic(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isPublic());
    }

    public function testGetPrototype(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertSame($methodReflection, $methodReflection->getPrototype());
    }

    public function testGetVariants(): void
    {
        $classReflection = $this->reflectionProvider->getClass(VisibilityEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $scope = $this->createMock(Scope::class);
        $parametersAcceptor = ParametersAcceptorSelector::selectFromArgs(
            $scope,
            [],
            $methodReflection->getVariants()
        );

        $this->assertSame(
            VisibilityEnum::class,
            $parametersAcceptor->getReturnType()->describe(VerbosityLevel::value())
        );
    }

    public function testGetDocComment(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DocCommentEnum::class);
        $docMethodRefl = $this->reflectionExtension->getMethod($classReflection, 'WITH_DOC_BLOCK');
        $noDocMethodRefl = $this->reflectionExtension->getMethod($classReflection, 'WITHOUT_DOC_BLOCK');

        // return null on no doc block
        $this->assertSame(null, $noDocMethodRefl->getDocComment());

        // return the correct doc block
        $this->assertMatchesRegularExpression('/With doc block/', $docMethodRefl->getDocComment());

        // remove @var declaration
        $this->assertDoesNotMatchRegularExpression('/@var/', $docMethodRefl->getDocComment());
    }

    public function testIsDeprecated(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DeprecatedEnum::class);
        $deprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'DEPRECATED');
        $notDeprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'NOT_DEPRECATED');

        $this->assertTrue($deprecatedRefl->isDeprecated()->yes());
        $this->assertTrue($notDeprecatedRefl->isDeprecated()->no());
    }

    public function testGetDeprecatedDescription(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DeprecatedEnum::class);
        $deprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'DEPRECATED');
        $notDeprecatedRefl = $this->reflectionExtension->getMethod($classReflection, 'NOT_DEPRECATED');

        $this->assertSame('Test deprecated reflection', $deprecatedRefl->getDeprecatedDescription());
        $this->assertNull($notDeprecatedRefl->getDeprecatedDescription());
    }

    public function testIsFinal(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DeprecatedEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isFinal()->no());
    }

    public function testIsInternal(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DeprecatedEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->isInternal()->no());
    }

    public function testGetThrowType(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DeprecatedEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertNull($methodReflection->getThrowType());
    }

    public function testHasSideEffects(): void
    {
        $classReflection = $this->reflectionProvider->getClass(DeprecatedEnum::class);
        $methodReflection = $this->reflectionExtension->getMethod($classReflection, 'STR');

        $this->assertTrue($methodReflection->hasSideEffects()->no());
    }
}
