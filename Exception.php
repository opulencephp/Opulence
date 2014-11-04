<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a general exception in the application
 */
namespace RDev\Exceptions;

class Exception extends \Exception
{
    /**
     * Gets the exception in an easy to read format
     *
     * @return string The string in an easily readable format
     */
    public function __toString()
    {
        $explodedClassName = explode("\\", get_called_class());

        return end($explodedClassName) . ": " . $this->message . ": Stack Trace: " . print_r($this->getTraceAsString(), true);
    }
} 