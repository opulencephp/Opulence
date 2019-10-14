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
use Aphiria\Console\StatusCodes;
use Aphiria\IO\FileSystem;
use Closure;
use Opulence\Framework\Console\ClassFileCompiler;

/**
 * Defines the base class for "make:" command handlers to extend
 */
abstract class MakeCommandHandler implements ICommandHandler
{
    /** @var ClassFileCompiler The compiler for class templates */
    protected ClassFileCompiler $classFileCompiler;
    /** @var FileSystem The file system */
    protected FileSystem $fileSystem;

    /**
     * @param ClassFileCompiler $classFileCompiler The compiler for class templates
     */
    protected function __construct(ClassFileCompiler $classFileCompiler)
    {
        $this->classFileCompiler = $classFileCompiler;
        $this->fileSystem = new FileSystem();
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $path = $this->classFileCompiler->compile(
            $input->arguments['class'],
            $this->getTemplateFilePath($input),
            $this->getCustomTagCompiler()
        );
        $output->writeln("<success>File was created at $path</success>");

        return StatusCodes::OK;
    }

    /**
     * Gets the path to the template file (done at runtime so we can potentially use input to determine the template)
     *
     * @param Input $input The input to use
     * @param IOutput $output The output to write to
     * @return string The path to the template file
     */
    abstract protected function getTemplateFilePath(Input $input, IOutput $output): string;

    /**
     * Gets the custom tag compiler
     *
     * @param Input $input The input to use
     * @param IOutput $output The output to write to
     * @return Closure|null The closure that takes in the contents of the compiled template and compiles any custom tags
     */
    protected function getCustomTagCompiler(Input $input, IOutput $output): ?Closure
    {
        return null;
    }
}
