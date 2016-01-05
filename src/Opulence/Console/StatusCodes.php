<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console;

/**
 * Defines different console status codes
 */
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