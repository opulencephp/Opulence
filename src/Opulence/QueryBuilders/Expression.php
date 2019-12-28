<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders;

/**
 * Expression is designed to be used for setting values in INSERT and UPDATE statements
 * It is not intended to be used in WHERE clauses or as columns in SELECT queries
 */
class Expression
{
    /** @var string The expression to use */
    protected $expression = '';

    /** @var array[] */
    protected $values = [];

    /**
     * Expression constructor.
     *
     * @param string $expression
     * @param mixed  ...$values
     *
     * @throws InvalidQueryException
     */
    public function __construct(string $expression, ...$values)
    {
        $this->expression = $expression;

        foreach ($values as $value) {
            if (is_scalar($value)) {
                $value = [$value, \PDO::PARAM_STR];
            }

            if (!is_array($value) || count($value) !== 2) {
                throw new InvalidQueryException('Incorrect number of items in expression value array');
            }

            if (!array_key_exists(0, $value) || !array_key_exists(1, $value)) {
                throw new InvalidQueryException('Incorrect keys in expression value array');
            }

            if (!is_scalar($value[0]) || !is_numeric($value[1]) || $value[1] < 0) {
                throw new InvalidQueryException('Incorrect expression values');
            }

            $this->values[] = $value;
        }
    }

    /**
     * @return array
     */
    public function getParameters() : array
    {
        return $this->values;
    }

    public function getSql() : string
    {
        return $this->expression;
    }
}
