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
 * Defines the not-in-array rule
 */
class NotInRule implements IRuleWithArgs
{
    /** @var array The value to compare against */
    protected $array = null;

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'notIn';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->array === null) {
            throw new LogicException('Array not set');
        }

        return !in_array($value, $this->array);
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) !== 1 || !is_array($args[0])) {
            throw new InvalidArgumentException('Must pass a list of values');
        }

        $this->array = $args[0];
    }
}
