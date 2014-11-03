<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks a builder for use in testing
 */
namespace RDev\Tests\Views\Mocks;
use RDev\Views;
use RDev\Views\Template;

class FooBuilder implements Views\IBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(Template $template)
    {
        $template->setTag("foo", "bar");

        return $template;
    }
}