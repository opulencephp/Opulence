<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for client rules to implement
 */
namespace RDev\Forms\Rules\Client;

interface IClientRule
{
    /**
     * Generates a client-side script to run to determine if input passes the rule
     *
     * @param string $inputName The name of the input to generate a rule for
     * @return string The client-side script to run to determine if input passes the rule
     */
    public function generateScript($inputName);
}