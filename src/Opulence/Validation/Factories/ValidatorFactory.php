<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Factories;

use Opulence\Validation\Rules\Factories\RulesFactory;
use Opulence\Validation\IValidator;
use Opulence\Validation\Validator;

/**
 * Defines the validator factory
 */
class ValidatorFactory implements IValidatorFactory
{
    /** @var RulesFactory The rules factory */
    protected $rulesFactory = null;

    /**
     * @param RulesFactory $rulesFactory The rules factory
     */
    public function __construct(RulesFactory $rulesFactory)
    {
        $this->rulesFactory = $rulesFactory;
    }

    /**
     * @inheritdoc
     */
    public function createValidator() : IValidator
    {
        return new Validator($this->rulesFactory);
    }
}