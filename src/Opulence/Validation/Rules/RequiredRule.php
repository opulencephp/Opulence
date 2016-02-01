<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use Countable;

/**
 * Defines the required rule
 */
class RequiredRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "required";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value) && mb_strlen($value) == 0) {
            return false;
        }

        if ((is_array($value) || $value instanceof Countable) && count($value) == 0) {
            return false;
        }

        return true;
    }
}