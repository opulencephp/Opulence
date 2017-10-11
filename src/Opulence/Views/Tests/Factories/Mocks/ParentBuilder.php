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
 * Mocks a builder that builds a parent for use in testing
 */
class ParentBuilder implements IViewBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IView $view) : IView
    {
        $view->setVar('foo', 'blah');
        $view->setVar('bar', true);

        return $view;
    }
}
