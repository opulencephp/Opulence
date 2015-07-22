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
    protected function createView($path, $content)
    {
        return new View($path, $content);
    }

    /**
     * @inheritDoc
     */
    protected function getExtension()
    {
        return "php";
    }
}