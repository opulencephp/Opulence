<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines the interface for view sub-compilers to implement
 */
namespace RDev\Views\Compilers\SubCompilers;
use RDev\Views;
use RDev\Views\Compilers;

interface ISubCompiler
{
    /**
     * Gets the compiled template
     *
     * @param Views\ITemplate $template The template to render
     * @param string $content The content to compile
     * @return string The compiled template
     * @throws Compilers\ViewCompilerException Thrown if there was an error compiling the template
     */
    public function compile(Views\ITemplate $template, $content);
}