<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Factories\Mocks;

use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

/**
 * Mocks a builder for use in testing
 */
class FooBuilder implements IViewBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IView $view) : IView
    {
        $view->setVar('foo', 'bar');

        return $view;
    }
}
