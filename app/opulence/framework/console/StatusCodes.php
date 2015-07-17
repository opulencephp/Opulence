<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines different console status codes
 */
namespace Opulence\Framework\Console;

class StatusCodes
{
    /** Everything executed successfully */
    const OK = 0;
    /** There was a warning */
    const WARNING = 1;
    /** There was a non-fatal error */
    const ERROR = 2;
    /** The application crashed */
    const FATAL = 3;
}