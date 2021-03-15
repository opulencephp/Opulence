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
use LogicException;

/**
 * Defines a regular expression rule
 */
class RegexRule implements IRuleWithArgs
{
    /** @var string The regular expression to run */
    protected $regex = null;

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return 'regex';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->regex === null) {
            throw new LogicException('Regex not set');
        }

        return preg_match($this->regex, $value) === 1;
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) !== 1 || !is_string($args[0])) {
            throw new InvalidArgumentException('Must pass a regex to compare against');
        }

        $this->regex = $args[0];
    }
}
