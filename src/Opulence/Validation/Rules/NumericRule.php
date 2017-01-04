<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Defines the numeric rule
 */
class NumericRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "numeric";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        return is_numeric($value);
    }
}
