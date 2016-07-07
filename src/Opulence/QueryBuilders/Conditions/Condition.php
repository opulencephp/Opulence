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
 * Defines the base condition
 */
abstract class Condition implements ICondition
{
    /** @var string The column */
    protected $column = "";
    
    /**
     * @param string $column The column
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }
}