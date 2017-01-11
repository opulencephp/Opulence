<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use BadMethodCallException;
use Countable;
use InvalidArgumentException;
use LogicException;
use Opulence\Validation\Rules\Errors\Compilers\ICompiler;
use Opulence\Validation\Rules\Errors\ErrorTemplateRegistry;

/**
 * Defines the rules for validation
 */
class Rules
{
    /** @var RuleExtensionRegistry The rule extension registry */
    protected $ruleExtensionRegistry = null;
    /** @var ErrorTemplateRegistry The error template registry */
    protected $errorTemplateRegistry = null;
    /** @var ICompiler The error template compiler */
    protected $errorTemplateCompiler = null;
    /** @var array The data used to compile error templates */
    protected $errorSlugsAndPlaceholders = [];
    /** @var IRule[] The list of rules */
    protected $rules = [];
    /** @var bool Whether or not a value is required */
    protected $isRequired = false;
    /** @var bool Whether or not we're building a conditional rule */
    protected $inCondition = false;

    /**
     * @param RuleExtensionRegistry $ruleExtensionRegistry The rule extension registry
     * @param ErrorTemplateRegistry $errorTemplateRegistry The error template registry
     * @param ICompiler $errorTemplateCompiler The error template compiler
     */
    public function __construct(
        RuleExtensionRegistry $ruleExtensionRegistry,
        ErrorTemplateRegistry $errorTemplateRegistry,
        ICompiler $errorTemplateCompiler
    ) {
        $this->ruleExtensionRegistry = $ruleExtensionRegistry;
        $this->errorTemplateRegistry = $errorTemplateRegistry;
        $this->errorTemplateCompiler = $errorTemplateCompiler;
    }

    /**
     * Attempts to call a rule extension
     *
     * @param string $methodName The method to call
     * @param array $args The arguments to pass
     * @return self For method chaining
     * @throws BadMethodCallException Thrown if no extension exists with the method name
     */
    public function __call(string $methodName, array $args) : self
    {
        if (!$this->ruleExtensionRegistry->hasRule($methodName)) {
            throw new BadMethodCallException("No rule extension with name \"$methodName\" exists");
        }

        $rule = $this->ruleExtensionRegistry->getRule($methodName);

        if ($rule instanceof IRuleWithArgs) {
            $rule->setArgs($args);
        }

        $this->addRule($rule);

        return $this;
    }

    /**
     * Marks a field as having to contain only alphabetic characters
     *
     * @return self For method chaining
     */
    public function alpha() : self
    {
        $this->createRule(AlphaRule::class);

        return $this;
    }

    /**
     * Marks a field as having to contain only alpha-numeric characters
     *
     * @return self For method chaining
     */
    public function alphaNumeric() : self
    {
        $this->createRule(AlphaNumericRule::class);

        return $this;
    }

    /**
     * Marks a field as having to be between values
     *
     * @param int|float $min The minimum value to compare against
     * @param int|float $max The maximum value to compare against
     * @param bool $isInclusive Whether or not the extremes are inclusive
     * @return self For method chaining
     */
    public function between($min, $max, bool $isInclusive = true) : self
    {
        $this->createRule(BetweenRule::class, [$min, $max, $isInclusive]);

        return $this;
    }

    /**
     * Specifies conditions that must be met for certain rules to be set
     *
     * @param callable $callback The callback to evaluate The variable list of rules
     *      It must accept an array of all values
     * @return self For method chaining
     * @throws LogicException Thrown if we were already in a condition
     */
    public function condition($callback) : self
    {
        if ($this->inCondition) {
            throw new LogicException('Cannot nest rule conditions');
        }

        // Order is important here
        $this->createRule(ConditionalRule::class, [$callback]);
        $this->inCondition = true;

        return $this;
    }

    /**
     * Marks a field as having to be a date in the input format(s)
     *
     * @param string|array $formats The expected formats
     * @return self For method chaining
     */
    public function date($formats) : self
    {
        $this->createRule(DateRule::class, [$formats]);

        return $this;
    }

    /**
     * Marks a field as having to be an email
     *
     * @return self For method chaining
     */
    public function email() : self
    {
        $this->createRule(EmailRule::class);

        return $this;
    }

    /**
     * Ends the condition stack
     *
     * @return self For method chaining
     */
    public function endCondition() : self
    {
        $this->inCondition = false;

        return $this;
    }

    /**
     * Marks a field as having to equal a value
     *
     * @param mixed $value The value that the field must equal
     * @return self For method chaining
     */
    public function equals($value) : self
    {
        $this->createRule(EqualsRule::class, [$value]);

        return $this;
    }

    /**
     * Marks a field as having to equal another field
     *
     * @param string $name The other field to equal
     * @return self For method chaining
     */
    public function equalsField(string $name) : self
    {
        $this->createRule(EqualsFieldRule::class, [$name]);

        return $this;
    }

    /**
     * Gets the error messages
     *
     * @param string $field The name of the field whose errors we're getting
     * @return array The list of errors
     */
    public function getErrors(string $field) : array
    {
        $compiledErrors = [];

        foreach ($this->errorSlugsAndPlaceholders as $errorData) {
            $compiledErrors[] = $this->errorTemplateCompiler->compile(
                $field,
                $this->errorTemplateRegistry->getErrorTemplate($field, $errorData['slug']),
                $errorData['placeholders']
            );
        }

        return $compiledErrors;
    }

    /**
     * Marks a field as having to be in a list of approved values
     *
     * @param array $array The list of approved values
     * @return self For method chaining
     */
    public function in(array $array) : self
    {
        $this->createRule(InRule::class, [$array]);

        return $this;
    }

    /**
     * Marks a field as having to be an integer
     *
     * @return self For method chaining
     */
    public function integer() : self
    {
        $this->createRule(IntegerRule::class);

        return $this;
    }

    /**
     * Marks a field as having to be an IP address
     *
     * @return self For method chaining
     */
    public function ipAddress() : self
    {
        $this->createRule(IPAddressRule::class);

        return $this;
    }

    /**
     * Marks a field as having a maximum acceptable value
     *
     * @param int|float $max The maximum value to compare against
     * @param bool $isInclusive Whether or not the maximum is inclusive
     * @return self For method chaining
     */
    public function max($max, bool $isInclusive = true) : self
    {
        $this->createRule(MaxRule::class, [$max, $isInclusive]);

        return $this;
    }

    /**
     * Marks a field as having a minimum acceptable value
     *
     * @param int|float $min The minimum value to compare against
     * @param bool $isInclusive Whether or not the minimum is inclusive
     * @return self For method chaining
     */
    public function min($min, bool $isInclusive = true) : self
    {
        $this->createRule(MinRule::class, [$min, $isInclusive]);

        return $this;
    }

    /**
     * Marks a field as having to not be in a list of unapproved values
     *
     * @param array $array The list of unapproved values
     * @return self For method chaining
     */
    public function notIn(array $array) : self
    {
        $this->createRule(NotInRule::class, [$array]);

        return $this;
    }

    /**
     * Marks a field as having to be numeric
     *
     * @return self For method chaining
     */
    public function numeric() : self
    {
        $this->createRule(NumericRule::class);

        return $this;
    }

    /**
     * Gets whether or not all the rules pass
     *
     * @param mixed $value The value to validate
     * @param array $allValues The list of all values
     * @param bool $haltFieldValidationOnFailure True if we want to not check any other rules for a field
     *      once one fails, otherwise false
     * @return bool True if all the rules pass, otherwise false
     */
    public function pass($value, array $allValues = [], bool $haltFieldValidationOnFailure = false) : bool
    {
        $this->errorSlugsAndPlaceholders = [];
        $passes = true;

        foreach ($this->rules as $rule) {
            // Non-required fields do not need to evaluate all rules when empty
            if (!$this->isRequired) {
                if (
                    $value === null ||
                    (is_string($value) && $value === '') ||
                    ((is_array($value) || $value instanceof Countable) && count($value) === 0)
                ) {
                    continue;
                }
            }

            $thisRulePasses = $rule->passes($value, $allValues);

            if (!$thisRulePasses) {
                $this->addError($rule);
            }

            if ($haltFieldValidationOnFailure) {
                if (!$thisRulePasses) {
                    return false;
                }
            } else {
                $passes = $thisRulePasses && $passes;
            }
        }

        return $passes;
    }

    /**
     * Marks a field as having to match a regular expression
     *
     * @param string $regex The regex to match
     * @return self For method chaining
     */
    public function regex(string $regex) : self
    {
        $this->createRule(RegexRule::class, [$regex]);

        return $this;
    }

    /**
     * Marks a field as required
     *
     * @return self For method chaining
     */
    public function required() : self
    {
        $this->createRule(RequiredRule::class);
        $this->isRequired = true;

        return $this;
    }

    /**
     * Adds an error
     *
     * @param IRule $rule The rule that failed
     */
    protected function addError(IRule $rule)
    {
        if ($rule instanceof ConditionalRule) {
            $rules = $rule->getRules();
        } else {
            $rules = [$rule];
        }

        foreach ($rules as $singleRule) {
            $this->errorSlugsAndPlaceholders[] = [
                'slug' => $singleRule->getSlug(),
                'placeholders' => $singleRule instanceof IRuleWithErrorPlaceholders ? $singleRule->getErrorPlaceholders() : []
            ];
        }
    }

    /**
     * Adds a rule to the list
     *
     * @param IRule $rule The rule to add
     */
    protected function addRule(IRule $rule)
    {
        if ($this->inCondition) {
            /** @var ConditionalRule $lastRule */
            $lastRule = $this->rules[count($this->rules) - 1];
            $lastRule->addRule($rule);
        } else {
            $this->rules[] = $rule;
        }
    }

    /**
     * Adds a rule with the input name and arguments
     *
     * @param string $className The fully name of the rule class, eg "Opulence\...\RequiredRule"
     * @param array $args The extra arguments
     * @throws InvalidArgumentException Thrown if no rule exists with the input name
     */
    protected function createRule(string $className, array $args = [])
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException("Class \"$className\" does not exist");
        }

        /** @var IRule|IRuleWithArgs $rule */
        $rule = new $className;

        if ($rule instanceof IRuleWithArgs) {
            $rule->setArgs($args);
        }

        $this->addRule($rule);
    }
}
