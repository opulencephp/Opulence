<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Views\Tests\Factories\Mocks;

use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

/**
 * Mocks a builder for use in testing
 */
class BarBuilder implements IViewBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IView $view) : IView
    {
        $view->setVar('bar', 'baz');

        return $view;
    }
}
