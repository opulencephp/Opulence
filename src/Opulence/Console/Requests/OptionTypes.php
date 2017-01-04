<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Requests;

/**
 * Defines the different types of options
 */
class OptionTypes
{
    /** The argument is required */
    const REQUIRED_VALUE = 1;
    /** The argument is optional */
    const OPTIONAL_VALUE = 2;
    /** The argument is not allowed */
    const NO_VALUE = 4;
    /** The argument is an array */
    const IS_ARRAY = 8;
}
