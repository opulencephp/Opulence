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
 * Tests the conditional query builder
 */
namespace RamODev\Storage\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../../../storage/rdbms/querybuilders/ConditionalQueryBuilder.php");

class ConditionalQueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a condition to an empty clause
     */
    public function testAddingConditionToEmptyClause()
    {
        $conditions = array();
        $queryBuilder = new ConditionalQueryBuilder();
        $conditions = $queryBuilder->addConditionToClause($conditions, "AND", "name = 'dave'");
        $this->assertEquals(array(array("operation" => "AND", "condition" => "name = 'dave'")), $conditions);
    }

    /**
     * Tests adding a condition to a non-empty clause
     */
    public function testAddingConditionToNonEmptyClause()
    {
        $conditions = array(array("operation" => "OR", "condition" => "email = 'foo@bar.com'"));
        $queryBuilder = new ConditionalQueryBuilder();
        $conditions = $queryBuilder->addConditionToClause($conditions, "AND", "name = 'dave'");
        $this->assertEquals(array(array("operation" => "OR", "condition" => "email = 'foo@bar.com'"), array("operation" => "AND", "condition" => "name = 'dave'")), $conditions);
    }

    /**
     * Tests adding an "AND"ed "WHERE" statement
     */
    public function testAndWhere()
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->andWhere("name = 'dave'");
        $this->assertEquals(array(array("operation" => "AND", "condition" => "name = 'dave'")), $queryBuilder->getWhereConditions());
    }

    /**
     * Tests getting the SQL for a conditional clause
     */
    public function testGettingSQL()
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->where("name = 'dave'")
            ->orWhere("email = 'foo@bar.com'")
            ->andWhere("awesome = true");
        $this->assertEquals(" WHERE (name = 'dave') OR (email = 'foo@bar.com') AND (awesome = true)", $queryBuilder->getClauseConditionSQL("WHERE", $queryBuilder->getWhereConditions()));
    }

    /**
     * Tests adding an "OR"ed "WHERE" statement
     */
    public function testOrWhere()
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->orWhere("name = 'dave'");
        $this->assertEquals(array(array("operation" => "OR", "condition" => "name = 'dave'")), $queryBuilder->getWhereConditions());
    }

    /**
     * Tests adding "WHERE" statement
     */
    public function testWhere()
    {
        $queryBuilder = new ConditionalQueryBuilder();
        $queryBuilder->where("name = 'dave'");
        $this->assertEquals(array(array("operation" => "AND", "condition" => "name = 'dave'")), $queryBuilder->getWhereConditions());
    }
} 