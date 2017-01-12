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
use LogicException;

/**
 * Defines the minimum rule
 */
class MinRule implements IRuleWithArgs, IRuleWithErrorPlaceholders
{
    /** @var int|float The minimum */
    protected $min = null;
    /** @var bool Whether or not the maximum is inclusive */
    protected $isInclusive = true;

    /**
     * @inheritdoc
     */
    public function getErrorPlaceholders() : array
    {
        return ['min' => $this->min];
    }

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'min';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->min === null) {
            throw new LogicException('Minimum value not set');
        }

        if ($this->isInclusive) {
            return $value >= $this->min;
        } else {
            return $value > $this->min;
        }
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) === 0 || !is_numeric($args[0])) {
            throw new InvalidArgumentException('Must pass a minimum value to compare against');
        }

        $this->min = $args[0];

        if (count($args) === 2 && is_bool($args[1])) {
            $this->isInclusive = $args[1];
        }
    }
}
