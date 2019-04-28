<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Http;

use Exception;

/**
 * Defines the exception thrown when the CSRF token is invalid
 */
class InvalidCsrfTokenException extends Exception
{
    // Don't do anything
}
