<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console\Commands;

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Aphiria\Console\Output\Prompts\Prompt;
use Aphiria\Console\StatusCodes;
use Aphiria\IO\FileSystem;
use Opulence\Framework\Composer\Composer;

/**
 * Defines the base class for "make:" command handlers to extend
 */
abstract class MakeCommandHandler implements ICommandHandler
{
    /** @var Prompt The console prompt */
    protected Prompt $prompt;
    /** @var FileSystem The file system */
    protected FileSystem $fileSystem;
    /** @var Composer The Composer wrapper */
    protected Composer $composer;

    /**
     * @param Prompt $prompt The console prompt
     * @param FileSystem $fileSystem The file system
     * @param Composer $composer The Composer wrapper
     */
    public function __construct(Prompt $prompt, FileSystem $fileSystem, Composer $composer)
    {
        $this->prompt = $prompt;
        $this->fileSystem = $fileSystem;
        $this->composer = $composer;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $fullyQualifiedClassName = $this->composer->getFullyQualifiedClassName(
            $input->arguments['class'],
            $this->getDefaultNamespace($this->composer->getRootNamespace())
        );
        $path = $this->composer->getClassPath($fullyQualifiedClassName);

        if ($this->fileSystem->exists($path)) {
            $output->writeln('<error>File already exists</error>');

            return StatusCodes::ERROR;
        }

        $this->makeDirectories($path);
        $compiledTemplate = $this->compile(
            $this->fileSystem->read($this->getFileTemplatePath()),
            $fullyQualifiedClassName
        );
        $this->fileSystem->write($path, $compiledTemplate);
        $output->writeln("<success>File was created at $path</success>");

        return StatusCodes::OK;
    }

    /**
     * Gets the path to the template
     *
     * @return string The template path
     */
    abstract protected function getFileTemplatePath(): string;

    /**
     * Compiles a template
     *
     * @param string $templateContents The template to compile
     * @param string $fullyQualifiedClassName The fully-qualified class name
     * @return string the compiled template
     */
    protected function compile(string $templateContents, string $fullyQualifiedClassName): string
    {
        $explodedClass = explode('\\', $fullyQualifiedClassName);
        $namespace = implode('\\', array_slice($explodedClass, 0, -1));
        $className = end($explodedClass);

        return str_replace(['{{namespace}}', '{{class}}'], [$namespace, $className], $templateContents);
    }

    /**
     * Gets the default namespace for a class
     * Let extending classes override this if they need to
     *
     * @param string $rootNamespace The root namespace
     * @return string The default namespace
     */
    protected function getDefaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace;
    }

    /**
     * Makes the necessary directories for a class
     *
     * @param string $path The fully-qualified class name
     */
    protected function makeDirectories(string $path): void
    {
        $directoryName = dirname($path);

        if (!$this->fileSystem->isDirectory($directoryName)) {
            $this->fileSystem->makeDirectory($directoryName, 0777, true);
        }
    }
}
