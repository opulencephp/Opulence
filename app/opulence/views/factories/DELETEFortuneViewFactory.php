<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Fortune view factory
 */
namespace Opulence\Views\Factories;
use Opulence\Views\FortuneView;

class FortuneViewFactory extends ViewFactory
{
    /**
     * @inheritDoc
     */
    protected function createView($path, $content)
    {
        return new FortuneView($path, $content);
    }

    /**
     * @inheritDoc
     */
    protected function getExtension()
    {
        return "fortune.php";
    }
}