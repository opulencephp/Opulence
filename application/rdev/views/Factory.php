<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the template factory
 */
namespace RDev\Views;
use RDev\Files;

class Factory implements IFactory
{
    /** @var Files\FileSystem The file system to read templates with */
    private $fileSystem = null;
    /** @var string The root directory of the templates */
    private $rootTemplateDirectory = "";
    /** @var array The mapping of template paths to a list of builders to run whenever the template is created */
    private $builders = [];

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
     * {@inheritdoc}
     */
    public function createTemplate($templatePath)
    {
        $templatePath = ltrim($templatePath, "/");
        $content = $this->fileSystem->read($this->rootTemplateDirectory . "/" . $templatePath);
        $template = new Template($content);
        $template = $this->runBuilders($templatePath, $template);

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    public function registerBuilder($templatePath, IBuilder $builder)
    {
        if(!isset($this->builders[$templatePath]))
        {
            $this->builders[$templatePath] = [];
        }

        $this->builders[$templatePath][] = $builder;
    }

    /**
     * Runs the builders for a template (if there any)
     *
     * @param string $templatePath The path of the template relative to the root template directory
     * @param Template $template The template to run builders on
     * @return Template The built template
     */
    private function runBuilders($templatePath, Template $template)
    {
        if(isset($this->builders[$templatePath]))
        {
            /** @var IBuilder $builder */
            foreach($this->builders[$templatePath] as $builder)
            {
                $template = $builder->build($template);
            }
        }

        return $template;
    }
}