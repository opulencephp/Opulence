<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view factories to implement
 */
namespace Opulence\Views\Factories;
use Opulence\Files\FileSystemException;
use Opulence\Views\ITemplate;

interface ITemplateFactory
{
    /**
     * Aliases a template path
     * Useful for registering builders for multiple views that use the same template
     *
     * @param string $alias The alias to use
     * @param string $templatePath The path relative to the root template directory
     */
    public function alias($alias, $templatePath);

    /**
     * Creates a template from the file at the input path
     * If any builders are registered for this template, they're run too
     *
     * @param string $name The alias or path relative to the root template directory
     * @return ITemplate The template with the contents from the path
     * @throws FileSystemException Thrown if the template does not exist
     */
    public function create($name);

    /**
     * Registers a builder for a particular template
     * Every time this template is created, the builders are run on it
     * Builders are run in the order they're registered
     *
     * @param string|array $names The alias(es) or path(s) of the template relative to the root template directory
     * @param callable $callback The callback that will return an instance of a builder
     */
    public function registerBuilder($names, callable $callback);
}