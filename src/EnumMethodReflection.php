<?php declare(strict_types=1);

namespace MabeEnumPHPStan;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Generic\TemplateTypeMap;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class EnumMethodReflection implements MethodReflection
{
    private ClassReflection $classReflection;

    private string $name;

    public function __construct(ClassReflection $classReflection, string $name)
    {
        $this->classReflection = $classReflection;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->classReflection;
    }

    public function isStatic(): bool
    {
        return true;
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this;
    }

    public function getVariants(): array
    {
        return [
            new FunctionVariant(
                TemplateTypeMap::createEmpty(),
                TemplateTypeMap::createEmpty(),
                [],
                false,
                new ObjectType($this->classReflection->getName())
            ),
        ];
    }

    public function getDocComment(): ?string
    {
        $docComment = $this->classReflection->getConstant($this->name)->getDocComment();

        if ($docComment) {
            // remove @var annotation of constant definition
            $docComment = preg_replace('/@var.*/', '', $docComment);
        }

        return $docComment;
    }

    public function isDeprecated(): TrinaryLogic
    {
        return $this->classReflection->getConstant($this->name)->isDeprecated();
    }

    public function getDeprecatedDescription(): ?string
    {
        return $this->classReflection->getConstant($this->name)->getDeprecatedDescription();
    }

    public function isFinal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function isInternal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getThrowType(): ?Type
    {
        return null;
    }

    public function hasSideEffects(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }
}
