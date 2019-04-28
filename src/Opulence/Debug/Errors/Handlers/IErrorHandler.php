<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Debug\Errors\Handlers;

use ErrorException;

/**
 * Defines the interface for error handlers to implement
 */
interface IErrorHandler
{
    /**
     * Handles an error
     *
     * @param int $level The level of the error
     * @param string $message The message
     * @param string $file The file the error occurred in
     * @param int $line The line number the error occurred at
     * @param array $context The symbol table
     * @throws ErrorException Thrown because the error is converted to an exception
     */
    public function handle(int $level, string $message, string $file = '', int $line = 0, array $context = []): void;

    /**
     * Handles a PHP shutdown
     */
    public function handleShutdown(): void;

    /**
     * Registers the handler with PHP
     */
    public function register(): void;
}
