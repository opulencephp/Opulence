<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the error log to write exceptions to
 */
namespace RamODev\Application\Shared\Exceptions;

class Log
{
    /**
     * Writes a message to the logs
     *
     * @param Exception|string $message The message or exception to write to the log
     */
    public static function write($message)
    {
        error_log($message);
    }
} 