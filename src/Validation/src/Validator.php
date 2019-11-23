<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation;

use Opulence\Validation\Rules\Errors\ErrorCollection;
use Opulence\Validation\Rules\RulesFactory;
use Opulence\Validation\Rules\Rules;

/**
 * Defines the validator
 */
final class Validator implements IValidator
{
    /** @var RulesFactory The rules factory */
    protected RulesFactory $rulesFactory;
    /** @var Rules[] The list of rules by field name */
    protected array $rulesByField = [];
    /** @var ErrorCollection The error collection */
    protected ErrorCollection $errors;

    /**
     * @param RulesFactory|null $rulesFactory The rules factory
     */
    public function __construct(RulesFactory $rulesFactory = null)
    {
        $this->errors = new ErrorCollection();
        $this->rulesFactory = $rulesFactory ?? new RulesFactory();
    }

    /**
     * @inheritdoc
     */
    public function field(string $name): Rules
    {
        if (!isset($this->rulesByField[$name])) {
            $this->rulesByField[$name] = $this->rulesFactory->createRules();
        }

        return $this->rulesByField[$name];
    }

    /**
     * @inheritdoc
     */
    public function getErrors(): ErrorCollection
    {
        return $this->errors;
    }

    /**
     * @inheritdoc
     */
    public function isValid(array $allValues, bool $haltFieldValidationOnFailure = false): bool
    {
        $this->errors = new ErrorCollection();
        $passes = true;

        foreach ($this->rulesByField as $name => $rules) {
            $value = $allValues[$name] ?? null;
            $fieldPasses = $rules->pass($value, $allValues, $haltFieldValidationOnFailure);
            $passes = $passes && $fieldPasses;

            if (!$fieldPasses) {
                $this->errors[$name] = $rules->getErrors($name);
            }
        }

        return $passes;
    }
}
