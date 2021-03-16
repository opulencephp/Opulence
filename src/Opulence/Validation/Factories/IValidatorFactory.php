<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Factories;

use Opulence\Validation\IValidator;

/**
 * Defines the interface for validator factories to implement
 */
interface IValidatorFactory
{
    /**
     * Creates a new validator
     *
     * @return IValidator The validator
     */
    public function createValidator() : IValidator;
}
