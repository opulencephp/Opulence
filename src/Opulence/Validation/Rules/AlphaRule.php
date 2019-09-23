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
 * Defines the alphabetic rule
 */
final class AlphaRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug(): string
    {
        return 'alpha';
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []): bool
    {
        return ctype_alpha($value) && strpos($value, ' ') === false;
    }
}
