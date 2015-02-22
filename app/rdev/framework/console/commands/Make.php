<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base class for "Make:" commands to extend
 */
namespace RDev\Framework\Console\Commands;
use RDev\Console\Commands;
use RDev\Console\Kernels;
use RDev\Console\Prompts;
use RDev\Console\Requests;
use RDev\Console\Responses;
use RDev\Files;
use RDev\Framework\Composer;

abstract class Make extends Commands\Command
{
    /** @var Prompts\Prompt The console prompt */
    protected $prompt = null;
    /** @var Files\FileSystem The file system */
    protected $fileSystem = null;
    /** @var Composer\Composer The Composer wrapper */
    protected $composer = null;

    /**
     * @param Prompts\Prompt $prompt The console prompt
     * @param Files\FileSystem $fileSystem The file system
     * @param Composer\Composer $composer The Composer wrapper
     */
    public function __construct(Prompts\Prompt $prompt, Files\FileSystem $fileSystem, Composer\Composer $composer)
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
     * {@inheritdoc}
     */
    protected function define()
    {
        $this->addArgument(new Requests\Argument(
            "class",
            Requests\ArgumentTypes::REQUIRED,
            "The name of the class to create"
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(Responses\IResponse $response)
    {
        $fullyQualifiedClassName = $this->composer->getFullyQualifiedClassName(
            $this->getArgumentValue("class"),
            $this->getDefaultNamespace($this->composer->getRootNamespace())
        );
        $path = $this->composer->getClassPath($fullyQualifiedClassName);

        if($this->fileSystem->exists($path))
        {
            $response->writeln("<error>File already exists</error>");

            return Kernels\StatusCodes::ERROR;
        }

        $this->makeDirectories($path);
        $compiledTemplate = $this->compile(
            $this->fileSystem->read($this->getFileTemplatePath()),
            $fullyQualifiedClassName
        );
        $this->fileSystem->write($path, $compiledTemplate);
        $response->writeln("<success>File was created</success>");

        return Kernels\StatusCodes::OK;
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

        if(!$this->fileSystem->isDirectory($directoryName))
        {
            $this->fileSystem->makeDirectory($directoryName, 0777, true);
        }
    }
}