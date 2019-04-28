<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Requests;

/**
 * Defines the different types of arguments
 */
class ArgumentTypes
{
    /** The argument is required */
    public const REQUIRED = 1;
    /** The argument is optional */
    public const OPTIONAL = 2;
    /** The argument is an array */
    public const IS_ARRAY = 4;
}
