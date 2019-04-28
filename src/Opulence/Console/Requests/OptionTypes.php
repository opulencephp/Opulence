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
 * Defines the different types of options
 */
class OptionTypes
{
    /** The argument is required */
    public const REQUIRED_VALUE = 1;
    /** The argument is optional */
    public const OPTIONAL_VALUE = 2;
    /** The argument is not allowed */
    public const NO_VALUE = 4;
    /** The argument is an array */
    public const IS_ARRAY = 8;
}
