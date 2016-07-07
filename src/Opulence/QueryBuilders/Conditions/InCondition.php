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

/**
 * Defines the IN condition
 */
class InCondition extends Condition
{
    /** @var string The sub-expression */
    protected $expression = "";
    /** @var array The list of parameters bound to the query */
    protected $parameters = [];
    /** @var bool True if we're using parameters, otherwise we're using a sub-expression */
    protected $usingParameters = true;
    
    /**
     * @inheritdoc
     * @param array|string $parametersOrExpression Either the parameters or the sub-expression
     * @throws InvalidArgumentException Thrown if the parameters are not in the correct format
     */
    public function __construct(string $column, $parametersOrExpression)
    {
        parent::__construct($column);
        
        if (is_string($parametersOrExpression)) {
            $this->usingParameters = false;
            $this->expression = $parametersOrExpression;
        } else if (is_array($parametersOrExpression)) {
            $this->usingParameters = true;
            $this->parameters = $parametersOrExpression;
        } else {
            throw new InvalidArgumentException("Must pass either parameters or sub-expression to IN condition");
        }
    }
    
    /**
     * @inheritdoc
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }
        
    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = "{$this->column} IN (";
        
        if ($this->usingParameters) {
            $sql .= implode(",", array_fill(0, count($this->parameters), "?"));
        } else {
            $sql .= $this->expression;
        }
        
        $sql .= ")";
        
        return $sql;
    }
}