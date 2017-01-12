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
 * Defines the maximum rule
 */
class MaxRule implements IRuleWithArgs, IRuleWithErrorPlaceholders
{
    /** @var int|float The maximum */
    protected $max = null;
    /** @var bool Whether or not the maximum is inclusive */
    protected $isInclusive = true;

    /**
     * @inheritdoc
     */
    public function getErrorPlaceholders() : array
    {
        return ['max' => $this->max];
    }

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'max';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->max === null) {
            throw new LogicException('Maximum value not set');
        }

        if ($this->isInclusive) {
            return $value <= $this->max;
        } else {
            return $value < $this->max;
        }
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) === 0 || !is_numeric($args[0])) {
            throw new InvalidArgumentException('Must pass a maximum value to compare against');
        }

        $this->max = $args[0];

        if (count($args) === 2 && is_bool($args[1])) {
            $this->isInclusive = $args[1];
        }
    }
}
