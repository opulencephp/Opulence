<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the template factory
 */
namespace RDev\Views\Templates;
use RDev\Files;

class TemplateFactory
{
    /** @var Files\FileSystem The file system to read templates with */
    private $fileSystem = null;
    /** @var string The root directory of the templates */
    private $rootTemplateDirectory = "";

    /**
     * @param Files\FileSystem $fileSystem The file system to read templates with
     * @param string $rootTemplateDirectory The root directory of the templates
     */
    public function __construct(Files\FileSystem $fileSystem, $rootTemplateDirectory)
    {
        $this->fileSystem = $fileSystem;
        $this->rootTemplateDirectory = rtrim($rootTemplateDirectory, "/");
    }

    /**
     * Creates a template from the file at the input path
     *
     * @param string $templatePath The path relative to the root template directory
     * @return Template The template with the contents from the path
     * @throws Files\FileSystemException Thrown if the template does not exist
     */
    public function create($templatePath)
    {
        $templatePath = ltrim($templatePath, "/");
        $content = $this->fileSystem->read($this->rootTemplateDirectory . "/" . $templatePath);

        return new Template($content);
    }
}