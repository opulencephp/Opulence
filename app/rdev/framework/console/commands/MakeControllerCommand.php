<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Makes a controller class
 */
namespace RDev\Framework\Console\Commands;

class MakeCommandControllerCommand extends MakeCommand
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:controller")
            ->setDescription("Creates a controller class");
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\HTTP\\Controllers";
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/Controller.template";
    }
}