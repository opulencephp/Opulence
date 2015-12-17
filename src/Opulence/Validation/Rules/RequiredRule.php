<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

/**
 * Defines the required rule
 */
class RequiredRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = [])
    {
        if ($value === null) {
            return false;
        }

        if (is_string($value) && mb_strlen($value) == 0) {
            return false;
        }

        return true;
    }
}