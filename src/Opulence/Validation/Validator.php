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
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\RuleExtensionRegistry;
use Opulence\Validation\Rules\Rules;

/**
 * Defines the validator
 */
class Validator implements IValidator
{
    /** @var RulesFactory The rules factory */
    protected $rulesFactory = null;
    /** @var RuleExtensionRegistry The rule extension registry */
    protected $ruleExtensionRegistry = null;
    /** @var Rules[] The list of rules by field name */
    protected $rulesByField = [];
    /** @var ErrorCollection The error collection */
    protected $errors = null;

    /**
     * @param RulesFactory $rulesFactory The rules factory
     * @param RuleExtensionRegistry $ruleExtensionRegistry The rule extension registry
     */
    public function __construct(RulesFactory $rulesFactory, RuleExtensionRegistry $ruleExtensionRegistry)
    {
        $this->errors = new ErrorCollection();
        $this->rulesFactory = $rulesFactory;
        $this->ruleExtensionRegistry = $ruleExtensionRegistry;
    }

    /**
     * @inheritdoc
     */
    public function field($name)
    {
        if (!isset($this->rulesByField[$name])) {
            $this->rulesByField[$name] = $this->rulesFactory->createRules();
        }

        return $this->rulesByField[$name];
    }

    /**
     * @inheritdoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function isValid(array $allValues, $haltFieldValidationOnFailure = false)
    {
        $this->errors = new ErrorCollection();
        $passes = true;

        foreach ($this->rulesByField as $name => $rules) {
            $value = isset($allValues[$name]) ? $allValues[$name] : null;
            $fieldPasses = $rules->pass($value, $allValues, $haltFieldValidationOnFailure);
            $passes = $passes && $fieldPasses;

            if (!$fieldPasses) {
                $this->errors[$name] = $rules->getErrors($name);
            }

        }

        return $passes;
    }

    /**
     * @inheritdoc
     */
    public function registerRule($rule, $slug = "")
    {
        $this->ruleExtensionRegistry->registerRuleExtension($rule, $slug);

        return $this;
    }
}