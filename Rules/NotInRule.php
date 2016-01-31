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
 * Defines the not-in-array rule
 */
class NotInRule extends InRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "notIn";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        return !parent::passes($value, $allValues);
    }
}