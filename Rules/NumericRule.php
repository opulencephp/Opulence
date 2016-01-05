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
 * Defines the numeric rule
 */
class NumericRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug()
    {
        return "numeric";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = [])
    {
        return is_numeric($value);
    }
}