<?php

declare(strict_types=1);

namespace MabeEnumPHPStanTest;

use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\NodeScopeResolver;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\ScopeContext;
use PHPStan\Broker\AnonymousClassNameHelper;
use PHPStan\Cache\Cache;
use PHPStan\File\FileHelper;
use PHPStan\Node\VirtualNode;
use PHPStan\PhpDoc\PhpDocNodeResolver;
use PHPStan\PhpDoc\PhpDocStringResolver;
use PHPStan\Testing\TestCase;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\FileTypeMapper;
use PHPStan\Type\VerbosityLevel;

abstract class ExtensionTestCase extends TestCase
{
    protected function processFile(
        string $file,
        string $expression,
        string $expectedType,
        DynamicMethodReturnTypeExtension $extension
    ): void {
        $broker = $this->createBroker([$extension]);
        $parser = $this->getParser();
        $currentWorkingDirectory = $this->getCurrentWorkingDirectory();
        $fileHelper = new FileHelper($currentWorkingDirectory);
        $typeSpecifier = $this->createTypeSpecifier(new Standard(), $broker);
        /** @var \PHPStan\PhpDoc\PhpDocStringResolver $phpDocStringResolver */
        $phpDocStringResolver = self::getContainer()->getByType(PhpDocStringResolver::class);
        $resolver = new NodeScopeResolver(
            $broker,
            $parser,
            new FileTypeMapper(
                $parser,
                $phpDocStringResolver,
                self::getContainer()->getByType(PhpDocNodeResolver::class),
                $this->createMock(Cache::class),
                $this->createMock(AnonymousClassNameHelper::class)
            ),
            $fileHelper,
            $typeSpecifier,
            true,
            true,
            true,
            [],
            []
        );
        $resolver->setAnalysedFiles([$fileHelper->normalizePath($file)]);

        $run = false;
        $resolver->processNodes(
            $parser->parseFile($file),
            $this->createScopeFactory($broker, $typeSpecifier)->create(ScopeContext::create($file)),
            function (Node $node, Scope $scope) use ($expression, $expectedType, &$run): void {
                if ($node instanceof VirtualNode) {
                    return;
                }
                if ((new Standard())->prettyPrint([$node]) !== 'die') {
                    return;
                }
                /** @var \PhpParser\Node\Stmt\Expression $expNode */
                $expNode = $this->getParser()->parseString(sprintf('<?php %s;', $expression))[0];
                self::assertSame($expectedType, $scope->getType($expNode->expr)->describe(VerbosityLevel::precise()));
                $run = true;
            }
        );
        self::assertTrue($run);
    }

    protected function processCode(
        string $code,
        string $expression,
        string $expectedType,
        DynamicMethodReturnTypeExtension $extension
    ): void {
        $fd     = tmpfile();
        $fdMeta = stream_get_meta_data($fd);
        $file   = $fdMeta['uri'];
        fwrite($fd, $code, strlen($code));

        $this->processFile($file, $expression, $expectedType, $extension);
    }
}