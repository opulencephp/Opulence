<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the error log to write exceptions to
 */
namespace RamODev\Exceptions;

class Log
{
    /**
     * Writes a message to our logs
     *
     * @param Exception|string $message The message or exception to write to our log
     */
    public static function write($message)
    {
        error_log($message);
    }
} 