<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Adds PostgreSQL-specific functionality for augmenting queries
 */
namespace RamODev\Databases\RDBMS\PostgreSQL\QueryBuilders;
use RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../querybuilders/AugmentingQueryBuilder.php");

class AugmentingQueryBuilder extends QueryBuilders\AugmentingQueryBuilder
{
    /** @var array The list of columns whose value we want to return */
    protected $returningExpressions = array();

    /**
     * Adds to a "RETURNING" clause
     *
     * @param string $expression,... A variable list of expressions to add to our "RETURNING" clause
     * @return $this
     */
    public function addReturning($expression)
    {
        $this->returningExpressions = array_merge($this->returningExpressions, func_get_args());

        return $this;
    }

    /**
     * Gets the SQL that makes up the "RETURNING" clause, if one was specified
     *
     * @return string The SQL making up the "RETURNING" clause
     */
    public function getReturningClauseSQL()
    {
        if(count($this->returningExpressions) > 0)
        {
            return " RETURNING " . implode(", ", $this->returningExpressions);
        }

        return "";
    }

    /**
     * Starts a "RETURNING" clause
     * Only call this method once per query because it will overwrite an previously-set "RETURNING" expressions
     *
     * @param string $expression,... A variable list of expressions to add to our "RETURNING" clause
     * @return $this
     */
    public function returning($expression)
    {
        $this->returningExpressions = func_get_args();

        return $this;
    }
} 