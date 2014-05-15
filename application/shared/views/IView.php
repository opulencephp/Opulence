<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for views to implement
 */
namespace RDev\Application\Shared\Views;

interface IView
{
    /**
     * Gets the output of the view
     *
     * @return string The output (eg HTML, text, etc)
     */
    public function getOutput();
} 