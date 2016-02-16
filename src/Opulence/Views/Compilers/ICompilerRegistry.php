<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers;

use InvalidArgumentException;
use Opulence\Views\IView;

/**
 * Defines the interface for compiler registries to implement
 */
interface ICompilerRegistry
{
    /**
     * Gets the compiler registered to the view
     *
     * @param IView $view
     * @return ICompiler The compiler registered to the view
     * @throws InvalidArgumentException Thrown if no compiler is registered to the view
     */
    public function getCompiler(IView $view) : ICompiler;

    /**
     * Registers a compiler for all view files with the input extension
     *
     * @param string $extension The extension (without preceding period) this compiler compiles
     * @param ICompiler $compiler The compiler for the input view class
     */
    public function registerCompiler(string $extension, ICompiler $compiler);
}