<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Rules;

use InvalidArgumentException;

/**
 * Defines the interface for rules with extra arguments
 */
interface IRuleWithArgs extends IRule
{
    /**
     * Sets the arguments the rule depends on
     *
     * @param array $args The list of arguments
     * @throws InvalidArgumentException Thrown if the arguments were invalid
     */
    public function setArgs(array $args);
}
