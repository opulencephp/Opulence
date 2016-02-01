<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Validation\Rules;

use InvalidArgumentException;
use LogicException;

/**
 * Defines the equals field rule
 */
class EqualsFieldRule implements IRuleWithArgs, IRuleWithErrorPlaceholders
{
    /** @var string The name of the field to compare to */
    protected $fieldName = null;

    /**
     * @inheritdoc
     */
    public function getErrorPlaceholders() : array
    {
        return ["other" => $this->fieldName];
    }

    /**
     * @inheritdoc
     */
    public function getSlug() : string
    {
        return "equalsField";
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = []) : bool
    {
        if ($this->fieldName === null) {
            throw new LogicException("Field name not set");
        }

        $comparisonValue = isset($allValues[$this->fieldName]) ? $allValues[$this->fieldName] : null;

        return $value === $comparisonValue;
    }

    /**
     * @inheritdoc
     */
    public function setArgs(array $args)
    {
        if (count($args) != 1 || !is_string($args[0])) {
            throw new InvalidArgumentException("Must pass valid field name");
        }

        $this->fieldName = $args[0];
    }
}