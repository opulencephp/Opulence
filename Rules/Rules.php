<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use BadMethodCallException;
use InvalidArgumentException;
use LogicException;
use ReflectionClass;

/**
 * Defines the rules for validation
 */
class Rules
{
    /** @var RuleExtensionRegistry The rule extension registry */
    protected $ruleExtensionRegistry = null;
    /** @var IRule[] The list of rules */
    protected $rules = [];
    /** @var bool Whether or not we're building a conditional rule */
    protected $inCondition = false;

    /**
     * @param RuleExtensionRegistry $ruleExtensionRegistry The rule extension registry
     */
    public function __construct(RuleExtensionRegistry $ruleExtensionRegistry)
    {
        $this->ruleExtensionRegistry = $ruleExtensionRegistry;
    }

    /**
     * Attempts to call a rule extension
     *
     * @param string $methodName The method to call
     * @param array $args The arguments to pass
     * @return $this For method chaining
     * @throws BadMethodCallException Thrown if no extension exists with the method name
     */
    public function __call($methodName, array $args)
    {
        if (!$this->ruleExtensionRegistry->has($methodName)) {
            throw new BadMethodCallException("No rule extension with name \"$methodName\" exists");
        }

        // Todo:  No way to pass $args
        $this->rules[] = $this->ruleExtensionRegistry->get($methodName);

        return $this;
    }

    /**
     * Specifies conditions that must be met for certain rules to be set
     *
     * @param callable $callback The callback to evaluate The variable list of rules
     *      It must accept an array of all values
     * @return $this For method chaining
     * @throws LogicException Thrown if we were already in a condition
     */
    public function condition($callback)
    {
        if ($this->inCondition) {
            throw new LogicException("Cannot nest rule conditions");
        }

        // Order is important here
        $this->createRule(ConditionalRule::class, [$callback]);
        $this->inCondition = true;

        return $this;
    }

    /**
     * Marks a field as having to be an email
     *
     * @return $this For method chaining
     */
    public function email()
    {
        $this->createRule(EmailRule::class);

        return $this;
    }

    /**
     * Ends the condition stack
     *
     * @return $this For method chaining
     */
    public function endCondition()
    {
        $this->inCondition = false;

        return $this;
    }

    /**
     * Marks a field as having to equal a value
     *
     * @param mixed $value The value that the field must equal
     * @return $this For method chaining
     */
    public function equals($value)
    {
        $this->createRule(EqualsRule::class, [$value]);

        return $this;
    }

    /**
     * Marks a field as having to equal another field
     *
     * @param string $name The other field to equal
     * @return $this For method chaining
     */
    public function equalsField($name)
    {
        $this->createRule(EqualsFieldRule::class, [$name]);

        return $this;
    }

    /**
     * Gets whether or not the rule passes
     *
     * @param string $name The name of the field to validate
     * @param mixed $value The value to validate
     * @param array $allValues The list of all values
     * @return bool True if the rule passes, otherwise false
     */
    public function passes($name, $value, array $allValues = [])
    {
        $passes = true;

        foreach ($this->rules as $rule) {
            $passes = $passes && $rule->passes($value, $allValues);
        }

        return $passes;
    }

    /**
     * Marks a field as required
     *
     * @return $this For method chaining
     */
    public function required()
    {
        $this->createRule(RequiredRule::class);

        return $this;
    }

    /**
     * Adds a rule with the input name and arguments
     *
     * @param string $className The name of the rule class
     * @param array $constructorArgs The constructor arguments
     * @return IRule The new rule
     * @throws InvalidArgumentException Thrown if no rule exists with the input name
     */
    protected function createRule($className, array $constructorArgs = [])
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class \"$className\" does not exist");
        }

        /** @var IRule $rule */
        $rule = (new ReflectionClass($className))->newInstanceArgs($constructorArgs);

        if ($this->inCondition) {
            /** @var ConditionalRule $lastRule */
            $lastRule = $this->rules[count($this->rules) - 1];
            $lastRule->addRule($rule);
        } else {
            $this->rules[] = $rule;
        }
    }
}