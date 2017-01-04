<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation;

use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\Rules\Rules;

/**
 * Defines the validator
 */
class Validator implements IValidator
{
    /** @var RulesFactory The rules factory */
    protected $rulesFactory = null;
    /** @var Rules[] The list of rules by field name */
    protected $rulesByField = [];
    /** @var ErrorCollection The error collection */
    protected $errors = null;

    /**
     * @param RulesFactory $rulesFactory The rules factory
     */
    public function __construct(RulesFactory $rulesFactory)
    {
        $this->errors = new ErrorCollection();
        $this->rulesFactory = $rulesFactory;
    }

    /**
     * @inheritdoc
     */
    public function field(string $name) : Rules
    {
        if (!isset($this->rulesByField[$name])) {
            $this->rulesByField[$name] = $this->rulesFactory->createRules();
        }

        return $this->rulesByField[$name];
    }

    /**
     * @inheritdoc
     */
    public function getErrors() : ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function isValid(array $allValues, bool $haltFieldValidationOnFailure = false) : bool
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
}
