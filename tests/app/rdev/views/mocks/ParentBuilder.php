<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a builder that builds a parent for use in testing
 */
namespace RDev\Tests\Views\Mocks;
use RDev\Views\IBuilder;
use RDev\Views\ITemplate;

class ParentBuilder implements IBuilder
{
    /**
     * {@inheritdoc}
     */
    public function build(ITemplate $template)
    {
        $template->setTag("foo", "blah");
        $template->setVar("bar", true);

        return $template;
    }
}