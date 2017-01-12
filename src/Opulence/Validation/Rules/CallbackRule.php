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
 * Defines the callback rule
 */
class CallbackRule implements IRuleWithArgs
{
    /** @var callable The callback to run */
    protected $callback = null;

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'callback';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->callback === null) {
            throw new LogicException('Callback not set');
        }

        return ($this->callback)($value, $allValues);
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) !== 1 || !is_callable($args[0])) {
            throw new InvalidArgumentException('Must pass valid callback');
        }

        $this->callback = $args[0];
    }
}
