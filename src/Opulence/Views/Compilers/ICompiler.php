<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Compilers;

use Exception;
use Opulence\Views\IView;
use Throwable;

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
     * @throws Exception|Throwable Thrown if there was an error compiling the view
     */
    public function compile(IView $view) : string;
}
