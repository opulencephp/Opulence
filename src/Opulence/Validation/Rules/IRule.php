<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Defines the interface for rules to implement
 */
interface IRule
{
    /**
     * Gets whether or not the rule passes
     *
     * @param mixed $value The value to validate
     * @param array $allValues The list of all values
     * @return bool True if the rule passes, otherwise false
     */
    public function passes($value, array $allValues = []);
}