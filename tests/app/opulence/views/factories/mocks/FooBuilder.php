<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a builder for use in testing
 */
namespace Opulence\Tests\Views\Factories\Mocks;

use Opulence\Views\Factories\IViewBuilder;
use Opulence\Views\IView;

class FooBuilder implements IViewBuilder
{
    /**
     * @inheritdoc
     */
    public function build(IView $view)
    {
        $view->setVar("foo", "bar");

        return $view;
    }
}