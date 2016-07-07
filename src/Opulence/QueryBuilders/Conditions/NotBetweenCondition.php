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
 * Defines the NOT BETWEEN condition
 */
class NotBetweenCondition extends BetweenCondition
{
    /**
     * @inheritdoc
     */
    public function getSql() : string
    {
        return "{$this->column} NOT BETWEEN ? AND ?";
    }
}