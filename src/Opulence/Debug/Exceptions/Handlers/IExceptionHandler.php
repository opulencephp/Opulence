<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Debug\Exceptions\Handlers;

use Throwable;

/**
 * Defines the interface for exception handlers to implement
 */
interface IExceptionHandler
{
    /**
     * Handles an exception
     *
     * @param Throwable $ex The exception to handle
     */
    public function handle($ex);

    /**
     * Registers the handler with PHP
     */
    public function register();
}
