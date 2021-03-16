<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Validation\Rules;

use InvalidArgumentException;

/**
 * Defines the equals rule
 */
class EqualsRule implements IRuleWithArgs
{
    /** @var mixed The value to compare against */
    protected $value = null;

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'equals';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        return $value === $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) !== 1) {
            throw new InvalidArgumentException('Must pass a value to compare against');
        }

        $this->value = $args[0];
    }
}
