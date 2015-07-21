<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view compilers to implement
 */
namespace Opulence\Views\Compilers;
use Opulence\Views\IView;

interface ICompiler
{
    /**
     * Gets the compiled view
     *
     * @param IView $view The view to render
     * @param string|null $contents The contents to compile, otherwise the view's contents will be compiled
     * @return string The compiled view
     * @throws ViewCompilerException Thrown if there was an error compiling the view
     */
    public function compile(IView $view, $contents = null);
}