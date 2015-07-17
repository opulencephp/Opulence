<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for hosts to implement
 */
namespace Opulence\Applications\Environments\Hosts;

interface IHost
{
    /**
     * Gets the value of the host name
     *
     * @return string The host
     */
    public function getValue();
}