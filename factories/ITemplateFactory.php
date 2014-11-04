<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for view factories to implement
 */
namespace RDev\Views\Factories;
use RDev\Files;
use RDev\Views;

interface ITemplateFactory
{
    /**
     * Creates a template from the file at the input path
     * If any builders are registered for this template, they're run too
     *
     * @param string $templatePath The path relative to the root template directory
     * @return Views\ITemplate The template with the contents from the path
     * @throws Files\FileSystemException Thrown if the template does not exist
     */
    public function create($templatePath);

    /**
     * Registers a builder for a particular template
     * Every time this template is created, the builders are run on it
     * Builders are run in the order they're registered
     *
     * @param string $templatePath The path of the template relative to the root template directory
     * @param callable $callback The callback that will return an instance of a builder
     */
    public function registerBuilder($templatePath, callable $callback);
}