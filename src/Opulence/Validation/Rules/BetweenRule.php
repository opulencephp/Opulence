<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use InvalidArgumentException;
use LogicException;

/**
 * Defines the between rule
 */
class BetweenRule implements IRuleWithArgs, IRuleWithErrorPlaceholders
{
    /** @var int|float The minimum */
    protected $min = null;
    /** @var int|float The maximum */
    protected $max = null;
    /** @var bool Whether or not the extremes are inclusive */
    protected $isInclusive = true;

    /**
     * @inheritdoc
     */
    public function getErrorPlaceholders() : array
    {
        return ["min" => $this->min, "max" => $this->max];
    }

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "between";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->min === null) {
            throw new LogicException("Minimum value not set");
        }

        if ($this->max === null) {
            throw new LogicException("Maximum value not set");
        }

        if ($this->isInclusive) {
            return $value >= $this->min && $value <= $this->max;
        } else {
            return $value > $this->min && $value < $this->max;
        }
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) < 2 || !is_numeric($args[0]) || !is_numeric($args[1])) {
            throw new InvalidArgumentException("Must pass minimum and maximum values to compare against");
        }

        $this->min = $args[0];
        $this->max = $args[1];

        if (count($args) == 3 && is_bool($args[2])) {
            $this->isInclusive = $args[2];
        }
    }
}