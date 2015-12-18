<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation;

use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\IRule;
use Opulence\Validation\Rules\Rules;

/**
 * Defines the interface for validators to implement
 */
interface IValidator
{
    /**
     * Creates rules for a field
     *
     * @param string $name The name of the field to create rules for
     * @return Rules The rules for the input field
     */
    public function field($name);

    /**
     * Gets the list of errors
     *
     * @return ErrorCollection The list of errors
     */
    public function getErrors();

    /**
     * Checks if a list of values are valid
     *
     * @param array $allValues The name => value mappings to validate
     * @param bool $haltFieldValidationOnFailure True if we want to not check any other rules for a field
     *      once one fails, otherwise false
     * @return bool True if the values were valid, otherwise false
     */
    public function isValid(array $allValues, $haltFieldValidationOnFailure = false);

    /**
     * Registers a rule extension
     *
     * @param IRule|callable $rule Either the rule object or callback (that accepts a value and list of all values) and
     *      returns true if the rule passes, otherwise false
     * @param string $slug The slug name of the rule (only used if the rule is a callback)
     * @return $this For method chaining
     */
    public function registerRule($rule, $slug = "");
}