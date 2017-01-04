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
 * Defines the alpha-numeric rule
 */
class AlphaNumericRule implements IRule
{
    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "alphaNumeric";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        return ctype_alnum($value) && strpos($value, " ") === false;
    }
}
