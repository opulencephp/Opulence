<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view builders to implement
 */
namespace Opulence\Views\Factories;
use Opulence\Views\IView;

interface IViewBuilder
{
    /**
     * Builds a view or a part of a view
     * Useful for centralizing creation of common components in views
     *
     * @param IView $view The view to build
     * @return IView The built view
     */
    public function build(IView $view);
}