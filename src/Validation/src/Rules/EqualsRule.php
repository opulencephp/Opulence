<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Validation\Rules;

use InvalidArgumentException;

/**
 * Defines the equals rule
 */
final class EqualsRule implements IRuleWithArgs
{
    /** @var mixed The value to compare against */
    protected $value;

    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'equals';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        return $value === $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args): void
    {
        if (count($args) !== 1) {
            throw new InvalidArgumentException('Must pass a value to compare against');
        }

        $this->value = $args[0];
    }
}
