<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Factories;

use Opulence\Views\IView;

/**
 * Defines the interface for view builders to implement
 */
interface IViewBuilder
{
    /**
     * Builds a view or a part of a view
     * Useful for centralizing creation of common components in views
     *
     * @param IView $view The view to build
     * @return IView The built view
     */
    public function build(IView $view) : IView;
}
