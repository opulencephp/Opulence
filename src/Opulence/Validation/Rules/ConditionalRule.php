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
 * Defines the conditional rule
 */
class ConditionalRule extends CallbackRule
{
    /** @var IRule[] The list of rules to evaluate if the condition is true */
    protected $rules = [];

    /**
     * Adds a rule to evaluate if the condition is true
     *
     * @param IRule $rule
     */
    public function addRule(IRule $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = [])
    {
        if (!call_user_func($this->callback, $value, $allValues)) {
            return true;
        }

        $passes = true;

        foreach ($this->rules as $rule) {
            if (!$rule->passes($value, $allValues)) {
                $passes = false;
            }
        }

        return $passes;
    }
}