<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for server rules to implement
 */
namespace RDev\HTTP\Forms\Rules\Server;
use RDev\HTTP\Requests;

interface IServerRule
{
    /**
     * Gets whether or not a server rule passes
     *
     * @param Requests\Request $request The current request
     * @return bool True if the server rule has passed, otherwise false
     */
    public function passes(Requests\Request $request);
}