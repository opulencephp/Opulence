<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Makes a command class
 */
namespace RDev\Framework\Console\Commands;

class MakeCommand extends Make
{
    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        parent::define();

        $this->setName("make:command")
            ->setDescription("Creates a command class");
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileTemplatePath()
    {
        return __DIR__ . "/templates/ConsoleCommand.template";
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . "\\Console\\Commands";
    }
}