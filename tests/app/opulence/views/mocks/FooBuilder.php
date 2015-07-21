<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a builder for use in testing
 */
namespace Opulence\Tests\Views\Mocks;
use Opulence\Views\IBuilder;
use Opulence\Views\IView;

class FooBuilder implements IBuilder
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