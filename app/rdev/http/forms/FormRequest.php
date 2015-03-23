<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a form request
 */
namespace RDev\HTTP\Forms;
use RDev\HTTP\Requests;

abstract class FormRequest
{
    /**
     * The mapping of input names to the server rule classes
     * The value for each input can either be a string or an array of strings
     *
     * @var array
     */
    protected $serverRules = [];
    /**
     * The mapping of input names to the server sanitizer classes
     * The value for each input can either be a string or an array of strings
     *
     * @var array
     */
    protected $sanitizers = [];
    /**
     * The mapping of input names to the client rule classes
     * The value for each input can either be a string or an array of strings
     *
     * @var array
     */
    protected $clientRules = [];
    /** @var array The mapping of input names to the error messages to be displayed on rule failure */
    protected $errorMessages = [];

    /**
     * Gets whether or not the form input was valid
     *
     * @param Requests\Request $request The request to validate
     * @return bool True if the input was valid, otherwise false
     */
    public function isValid(Requests\Request $request)
    {
        // TODO
    }
}