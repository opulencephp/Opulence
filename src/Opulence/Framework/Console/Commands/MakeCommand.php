<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Requests\Argument;
use Opulence\Console\Requests\ArgumentTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Console\StatusCodes;
use Opulence\Files\FileSystem;
use Opulence\Framework\Composer\Composer;

/**
 * Defines the base class for "make:" commands to extend
 */
abstract class MakeCommand extends Command
{
    /** @var Prompt The console prompt */
    protected $prompt = null;
    /** @var FileSystem The file system */
    protected $fileSystem = null;
    /** @var Composer The Composer wrapper */
    protected $composer = null;

    /**
     * @param Prompt $prompt The console prompt
     * @param FileSystem $fileSystem The file system
     * @param Composer $composer The Composer wrapper
     */
    public function __construct(Prompt $prompt, FileSystem $fileSystem, Composer $composer)
    {
        parent::__construct();

        $this->prompt = $prompt;
        $this->fileSystem = $fileSystem;
        $this->composer = $composer;
    }

    /**
     * Gets the path to the template
     *
     * @return string The template path
     */
    abstract protected function getFileTemplatePath();

    /**
     * Compiles a template
     *
     * @param string $templateContents The template to compile
     * @param string $fullyQualifiedClassName The fully-qualified class name
     * @return string the compiled template
     */
    protected function compile($templateContents, $fullyQualifiedClassName)
    {
        $explodedClass = explode("\\", $fullyQualifiedClassName);
        $namespace = implode("\\", array_slice($explodedClass, 0, -1));
        $className = end($explodedClass);
        $compiledTemplate = str_replace("{{namespace}}", $namespace, $templateContents);
        $compiledTemplate = str_replace("{{class}}", $className, $compiledTemplate);

        return $compiledTemplate;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->addArgument(new Argument(
            "class",
            ArgumentTypes::REQUIRED,
            "The name of the class to create"
        ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $fullyQualifiedClassName = $this->composer->getFullyQualifiedClassName(
            $this->getArgumentValue("class"),
            $this->getDefaultNamespace($this->composer->getRootNamespace())
        );
        $path = $this->composer->getClassPath($fullyQualifiedClassName);

        if ($this->fileSystem->exists($path)) {
            $response->writeln("<error>File already exists</error>");

            return StatusCodes::ERROR;
        }

        $this->makeDirectories($path);
        $compiledTemplate = $this->compile(
            $this->fileSystem->read($this->getFileTemplatePath()),
            $fullyQualifiedClassName
        );
        $this->fileSystem->write($path, $compiledTemplate);
        $response->writeln("<success>File was created</success>");

        return StatusCodes::OK;
    }

    /**
     * Gets the default namespace for a class
     * Let extending classes override this if they need to
     *
     * @param string $rootNamespace The root namespace
     * @return string The default namespace
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Makes the necessary directories for a class
     *
     * @param string $path The fully-qualified class name
     */
    protected function makeDirectories($path)
    {
        $directoryName = dirname($path);

        if (!$this->fileSystem->isDirectory($directoryName)) {
            $this->fileSystem->makeDirectory($directoryName, 0777, true);
        }
    }
}