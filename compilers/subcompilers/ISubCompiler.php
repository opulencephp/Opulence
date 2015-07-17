<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view sub-compilers to implement
 */
namespace Opulence\Views\Compilers\SubCompilers;
use Opulence\Views\Compilers\ViewCompilerException;
use Opulence\Views\ITemplate;

interface ISubCompiler
{
    /**
     * Gets the compiled template
     *
     * @param ITemplate $template The template to render
     * @param string $content The content to compile
     * @return string The compiled template
     * @throws ViewCompilerException Thrown if there was an error compiling the template
     */
    public function compile(ITemplate $template, $content);
}