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
 * Defines the equals field rule
 */
class EqualsFieldRule implements IRule
{
    /** @var string The name of the field to compare to */
    protected $fieldName = "";

    /**
     * @param string $fieldName the name of the field to compare to
     */
    public function __construct($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = [])
    {
        $comparisonValue = isset($allValues[$this->fieldName]) ? $allValues[$this->fieldName] : null;

        return $value === $comparisonValue;
    }
}