<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a builder that builds a parent for use in testing
 */
namespace RDev\Tests\Views\Mocks;
use RDev\Views;

class ParentBuilder implements Views\IBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(Views\ITemplate $template)
    {
        $template->setTag("foo", "blah");
        $template->setVar("bar", true);

        return $template;
    }
}