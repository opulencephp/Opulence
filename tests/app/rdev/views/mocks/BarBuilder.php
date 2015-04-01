<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a builder for use in testing
 */
namespace RDev\Tests\Views\Mocks;
use RDev\Views\IBuilder;
use RDev\Views\ITemplate;

class BarBuilder implements IBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(ITemplate $template)
    {
        $template->setTag("bar", "baz");

        return $template;
    }
}