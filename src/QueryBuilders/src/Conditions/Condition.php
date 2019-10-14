<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Conditions;

/**
 * Defines the base condition
 */
abstract class Condition implements ICondition
{
    /** @var string The column */
    protected string $column = '';

    /**
     * @param string $column The column
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }
}
