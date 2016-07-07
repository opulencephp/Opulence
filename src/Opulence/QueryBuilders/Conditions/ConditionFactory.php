<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\Conditions;

use InvalidArgumentException;
use PDO;

/**
 * Defines the condition factory
 */
class ConditionFactory
{
    /**
     * Creates a new BETWEEN condition
     * 
     * @param string $column The name of the column
     * @param mixed $min The min value
     * @param mixed $max The max value
     * @param int $dataType The PDO data type for the min and max
     * @return BetweenCondition The condition
     */
    public function between(string $column, $min, $max, $dataType = PDO::PARAM_STR) : BetweenCondition
    {
        return new BetweenCondition($column, $min, $max, $dataType);
    }
    
    /**
     * Creates a new IN condition
     * 
     * @param string $column The name of the column
     * @param array|string $parametersOrExpression Either the parameters or the sub-expression
     * @return InCondition The condition
     * @throws InvalidArgumentException Thrown if the parameters are not in the correct format
     */
    public function in(string $column, $parametersOrExpression) : InCondition
    {
        return new InCondition($column, $parametersOrExpression);
    }
    
    /**
     * Creates a new NOT BETWEEN condition
     * 
     * @param string $column The name of the column
     * @param mixed $min The min value
     * @param mixed $max The max value
     * @param int $dataType The PDO data type for the min and max
     * @return NotBetweenCondition The condition
     */
    public function notBetween(string $column, $min, $max, $dataType = PDO::PARAM_STR) : NotBetweenCondition
    {
        return new NotBetweenCondition($column, $min, $max, $dataType);
    }
    
    /**
     * Creates a new NOT IN condition
     * 
     * @param string $column The name of the column
     * @param array|string $parametersOrExpression Either the parameters or the sub-expression
     * @return NotInCondition The condition
     * @throws InvalidArgumentException Thrown if the parameters are not in the correct format
     */
    public function notIn(string $column, $parametersOrExpression) : NotInCondition
    {
        return new NotInCondition($column, $parametersOrExpression);
    }
}