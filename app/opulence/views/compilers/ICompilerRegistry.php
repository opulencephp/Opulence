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
     * Registers a compiler for all instances of the input view class
     *
     * @param string $viewClass The class name of the view that this compiler handles
     * @param ICompiler $compiler The compiler for the input view class
     */
    public function registerCompiler($viewClass, ICompiler $compiler);
}