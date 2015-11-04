<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Compilers;

use Opulence\Views\IView;

/**
 * Defines the interface for view compilers to implement
 */
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