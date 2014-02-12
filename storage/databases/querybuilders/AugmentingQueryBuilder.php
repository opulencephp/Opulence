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
 * Builds parts of a query that augment (INSERT/UPDATE)
 */
namespace RamODev\Storage\Databases\QueryBuilders;

class AugmentingQueryBuilder
{
    /** @var array The mapping of column names to their respective values */
    protected $columnNamesToValues = array();

    /**
     * Adds column values to our query
     *
     * @param array $columnNamesToValues The mapping of column names to their respective values
     * @returns $this
     */
    public function addColumnValues($columnNamesToValues)
    {
        $this->columnNamesToValues = array_merge($this->columnNamesToValues, $columnNamesToValues);

        return $this;
    }

    /**
     * @return array
     */
    public function getColumnNamesToValues()
    {
        return $this->columnNamesToValues;
    }
} 