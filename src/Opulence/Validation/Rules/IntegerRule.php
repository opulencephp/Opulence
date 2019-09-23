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

/**
 * Defines the integer rule
 */
final class IntegerRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'integer';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
