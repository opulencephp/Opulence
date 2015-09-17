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
     * @return string The compiled view
     * @throws ViewCompilerException Thrown if there was an error compiling the view
     */
    public function compile(IView $view);
}