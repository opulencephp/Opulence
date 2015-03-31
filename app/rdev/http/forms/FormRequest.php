<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a form request
 */
namespace RDev\HTTP\Forms;
use InvalidArgumentException;
use RDev\HTTP\Forms\Rules\Client\IClientRule;
use RDev\HTTP\Forms\Rules\Server\IServerRule;
use RDev\HTTP\Requests\Request;

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
     * The value for each input can nafg82  either be a string or an array of strings
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
     * Creates the client-side scripts to run to validate input on the client
     *
     * @return string The client-side scripts
     * @throws InvalidArgumentException Thrown if the client-side rule is invalid
     */
    public function generateClientScripts()
    {
        $output = "";

        foreach($this->clientRules as $inputName => $ruleClass)
        {
            if(!class_exists($ruleClass))
            {
                throw new InvalidArgumentException("Client-side rule class \"$ruleClass\" does not exist");
            }

            // TODO:  How do we instantiate rules?  IoC?  Lock down the constructor?
            $rule = new $ruleClass();

            if(!$rule instanceof IClientRule)
            {
                throw new InvalidArgumentException("Client-side rule class \"$ruleClass\" does not implement IClientRule");
            }

            $output .= $rule->generateScript($inputName);
        }

        return $output;
    }

    /**
     * Gets whether or not the form input was valid
     *
     * @param Request $request The request to validate
     * @return bool True if the input was valid, otherwise false
     * @throws InvalidArgumentException Thrown if the server-side rule is invalid
     */
    public function isValid(Request $request)
    {
        $isValid = true;

        foreach($this->serverRules as $inputName => $ruleClass)
        {
            if(!class_exists($ruleClass))
            {
                throw new InvalidArgumentException("Server-side rule class \"$ruleClass\" does not exist");
            }

            // TODO:  How do we instantiate rules?  IoC?  Lock down the constructor?
            $rule = new $ruleClass();

            if(!$rule instanceof IServerRule)
            {
                throw new InvalidArgumentException("Server-side rule class \"$ruleClass\" does not implement IServerRule");
            }

            // TODO:  Set $value to correct value depending on the request method
            // TODO:  How do we handle a non-required value that is not set?  Need place to set required => false.  Where?
            $value = "";

            if(!$rule->passes($value, $request))
            {
                $isValid = false;

                if(isset($this->errorMessages[$inputName]))
                {
                    // TODO:  What do we do with the error message?
                }
            }
        }

        return $isValid;
    }
}