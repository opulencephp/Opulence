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
 * Defines the equals rule
 */
class EqualsRule implements IRule
{
    /** @var mixed The value to compare against */
    protected $value = null;

    /**
     * @param mixed $value The value to compare against
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function passes($value, array $allValues = [])
    {
        return $value === $this->value;
    }
}