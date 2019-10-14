<?php

/**
 * Aphiria
 *
 * @link      https://www.aphiria.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/aphiria/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Console;

use Aphiria\IO\FileSystem;
use Opulence\Framework\Console\ClassFileCompiler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Tests the class file compiler
 */
class ClassFileCompilerTest extends TestCase
{
    private static string $composerConfigPath = __DIR__ . '/../../../../composer.json';
    private static string $templateFilePath = __DIR__ . '/files/Class.template';
    private ClassFileCompiler $compiler;

    protected function setUp(): void
    {
        $this->compiler = new ClassFileCompiler(self::$composerConfigPath);
    }

    public function testExceptionThrownWhenCompilingClassThatAlreadyExists(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('File already exists');
        $this->compiler->compile(ClassFileCompiler::class, self::$templateFilePath);
    }

    public function testCompilingClassInValidDirectoryCreatesCorrectPath(): void
    {
        $expectedPath = __DIR__ . '/../../../../src/Framework/Console/Foo.php';
        /** @var FileSystem|MockObject $fileSystem */
        $fileSystem = $this->getMockBuilder(FileSystem::class)
            ->onlyMethods(['read', 'write'])
            ->getMock();
        $fileSystem->expects($this->at(0))
            ->method('read')
            ->with(self::$templateFilePath)
            ->willReturn(\file_get_contents(self::$templateFilePath));
        $fileSystem->expects($this->at(1))
            ->method('write')
            ->with($expectedPath, $this->getExpectedCompiledContents('Opulence\\Framework\\Console', 'Foo'));
        $compiler = new ClassFileCompiler(self::$composerConfigPath, $fileSystem);
        $this->assertEquals($expectedPath, $compiler->compile('Opulence\\Framework\\Console\\Foo', self::$templateFilePath));
    }

    /**
     * Gets the expected compiled contents
     *
     * @param string $namespace The expected namespace
     * @param string $className The expected class name
     * @return string The expected compiled contents
     */
    private function getExpectedCompiledContents(string $namespace, string $className): string
    {
        return \str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $className],
            \file_get_contents(self::$templateFilePath)
        );
    }
}
