<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use InvalidArgumentException;

/**
 * Defines the rule extension registry
 */
class RuleExtensionRegistry
{
    /** @var IRule[] The list of rule extensions */
    protected $extensions = [];

    /**
     * Gets the rule extension with the input name
     *
     * @param string $ruleName The name of the rule
     * @return IRule The rule extension
     * @throws InvalidArgumentException Thrown if no extension is registered with the name
     */
    public function getRule(string $ruleName) : IRule
    {
        if (!$this->hasRule($ruleName)) {
            throw new InvalidArgumentException("No rule extension with name \"$ruleName\" found");
        }

        return $this->extensions[$ruleName];
    }

    /**
     * Gets whether or not a rule extension exists with the input name
     *
     * @param string $ruleName The name of the rule to search for
     * @return bool Whether or not the rule extension exists
     */
    public function hasRule(string $ruleName) : bool
    {
        return isset($this->extensions[$ruleName]);
    }

    /**
     * Registers a rule extension
     *
     * @param IRule|callable $rule Either the rule object or callback (that accepts a value and list of all values) and
     *      returns true if the rule passes, otherwise false
     * @param string $slug The slug name of the rule (only used if the rule is a callback)
     * @throws InvalidArgumentException Thrown if the rule was incorrectly formatted
     */
    public function registerRuleExtension($rule, string $slug = "")
    {
        if ($rule instanceof IRule) {
            $slug = $rule->getSlug();
        }

        if (is_callable($rule)) {
            $callback = $rule;
            $rule = new CallbackRule();
            $rule->setArgs([$callback]);
        }

        if (!$rule instanceof IRule) {
            throw new InvalidArgumentException("Rule must either be a callback or implement IRule");
        }

        $this->extensions[$slug] = $rule;
    }
}