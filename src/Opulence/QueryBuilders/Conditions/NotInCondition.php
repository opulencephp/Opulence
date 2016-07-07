<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\QueryBuilders\Conditions;

/**
 * Defines the NOT IN condition
 */
class NotInCondition extends InCondition
{
    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        $sql = "{$this->column} NOT IN (";
        
        if ($this->usingParameters) {
            $sql .= implode(",", array_fill(0, count($this->parameters), "?"));
        } else {
            $sql .= $this->expression;
        }
        
        $sql .= ")";
        
        return $sql;
    }
}