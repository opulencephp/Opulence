<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Debug\Exceptions;

use ErrorException;

/**
 * Defines a wrapper for a fatal error exception
 */
class FatalErrorException extends ErrorException
{
    // Don't do anything
}
