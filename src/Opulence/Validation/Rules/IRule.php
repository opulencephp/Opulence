<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use LogicException;

/**
 * Defines the interface for rules to implement
 */
interface IRule
{
    /**
     * Gets the slug (short-name) for the rule
     *
     * @return string The slug
     */
    public function getSlug() : string;

    /**
     * Gets whether or not the rule passes
     *
     * @param mixed $value The value to validate
     * @param array $allValues The list of all values
     * @return bool True if the rule passes, otherwise false
     * @throws LogicException Thrown if the rule was not set up correctly
     */
    public function passes($value, array $allValues = []) : bool;
}
