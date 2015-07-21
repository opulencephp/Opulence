<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the PHP view factory
 */
namespace Opulence\Views\Factories;
use Opulence\Views\View;

class PHPViewFactory extends ViewFactory
{
    /**
     * @inheritDoc
     */
    protected function createViewFromContent($content)
    {
        return new View($content);
    }

    /**
     * @inheritDoc
     */
    protected function getExtension()
    {
        return "php";
    }
}