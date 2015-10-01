<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for compiler registries to implement
 */
namespace Opulence\Views\Compilers;

use InvalidArgumentException;
use Opulence\Views\IView;

interface ICompilerRegistry
{
    /**
     * Gets the compiler registered to the view
     *
     * @param IView $view
     * @return ICompiler The compiler registered to the view
     * @throws InvalidArgumentException Thrown if no compiler is registered to the view
     */
    public function get(IView $view);

    /**
     * Registers a compiler for all view files with the input extension
     *
     * @param string $extension The extension (without preceding period) this compiler compiles
     * @param ICompiler $compiler The compiler for the input view class
     */
    public function registerCompiler($extension, ICompiler $compiler);
}