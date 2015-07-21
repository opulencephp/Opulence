<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a builder for use in testing
 */
namespace Opulence\Tests\Views\Mocks;
use Opulence\Views\IBuilder;
use Opulence\Views\IFortuneView;

class BarBuilder implements IBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(IFortuneView $view)
    {
        $view->setTag("bar", "baz");

        return $view;
    }
}