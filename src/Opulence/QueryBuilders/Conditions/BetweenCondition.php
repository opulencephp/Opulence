<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\Conditions;

use PDO;

/**
 * Defines the BETWEEN condition
 */
class BetweenCondition extends Condition
{
    /** @var mixed The min value */
    protected $min = "";
    /** @var mixed The min value */
    protected $max = "";
    /** @var int The data type of the min/max */
    protected $dataType = PDO::PARAM_STR;
    
    /**
     * @inheritdoc
     * @param mixed $min The min value
     * @param mixed $max The max value
     * @param int $dataType The PDO data type for the min and max
     */
    public function __construct(string $column, $min, $max, int $dataType = PDO::PARAM_STR)
    {
        parent::__construct($column);
        
        $this->min = $min;
        $this->max = $max;
        $this->dataType = $dataType;
    }
    
    /**
     * @inheritdoc
     */
    public function getParameters() : array
    {
        return [[$this->min, $this->dataType], [$this->max, $this->dataType]];
    }
        
    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        return "{$this->column} BETWEEN ? AND ?";
    }
}