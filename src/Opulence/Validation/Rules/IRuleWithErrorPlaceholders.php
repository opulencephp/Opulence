<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Defines the interface for rules with error message placeholders
 */
interface IRuleWithErrorPlaceholders extends IRule
{
    /**
     * Gets the keyed array of error placeholders
     *
     * @return array The keyed array of placeholders => values
     */
    public function getErrorPlaceholders() : array;
}