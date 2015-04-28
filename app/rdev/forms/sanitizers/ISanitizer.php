<?php
/**
 * Copyright (C) 2015 David Young
 *
 * The interface for form input sanitizers to implement
 */
namespace RDev\Forms\Sanitizers;
use RDev\HTTP\Requests\Request;

interface ISanitizer
{
    /**
     * Sanitizes a value for use in the business logic
     *
     * @param mixed $value The value of the input to sanitize
     * @param Request $request The current request
     * @return mixed The sanitized value
     */
    public function sanitize($value, Request $request);
}