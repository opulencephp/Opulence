<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\TestsTemp;

use Opulence\QueryBuilders\Conditions\ICondition;
use Opulence\QueryBuilders\SelectQuery;
use PDO;

/**
 * Tests the select query
 */
class SelectQueryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ICondition The condition to use in tests */
    private $condition;

    protected function setUp(): void
    {
        $this->condition = $this->createMock(ICondition::class);
        $this->condition->expects($this->any())
            ->method('getSql')
            ->willReturn('c1 IN (?)');
        $this->condition->expects($this->any())
            ->method('getParameters')
            ->willReturn([[1, PDO::PARAM_INT]]);
    }

    /**
     * Tests adding a "GROUP BY" statement to one that was already started
     */
    public function testAddingGroupBy(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->groupBy('id')
            ->addGroupBy('name');
        $this->assertEquals('SELECT id, name FROM users GROUP BY id, name', $query->getSql());
    }

    /**
     * Tests adding an "AND"ed and an "OR"ed "WHERE" clause
     */
    public function testAddingOrWhereAndWhere(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10')
            ->orWhere("name <> 'dave'")
            ->andWhere("name <> 'brian'");
        $this->assertEquals(
            "SELECT id FROM users WHERE (id > 10) OR (name <> 'dave') AND (name <> 'brian')",
            $query->getSql()
        );
    }

    /**
     * Tests adding an "ORDER BY" statement to one that was already started
     */
    public function testAddingOrderBy(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->orderBy('id ASC')
            ->addOrderBy('name DESC');
        $this->assertEquals('SELECT id, name FROM users ORDER BY id ASC, name DESC', $query->getSql());
    }

    public function testAddingSelectExpression(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->addSelectExpression('name');
        $this->assertEquals('SELECT id, name FROM users', $query->getSql());
    }

    /**
     * Tests adding a "HAVING" condition that will be "AND"ed
     */
    public function testAndHaving(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having('COUNT(name) > 1')
            ->andHaving('COUNT(name) < 5');
        $this->assertEquals(
            'SELECT name FROM users GROUP BY name HAVING (COUNT(name) > 1) AND (COUNT(name) < 5)',
            $query->getSql()
        );
    }

    /**
     * Tests adding a "HAVING" condition object that will be "AND"ed
     */
    public function testAndHavingConditionObject(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having('COUNT(name) > 1')
            ->andHaving($this->condition);
        $this->assertEquals(
            'SELECT name FROM users GROUP BY name HAVING (COUNT(name) > 1) AND (c1 IN (?))',
            $query->getSql()
        );
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests adding a "WHERE" condition that will be "AND"ed
     */
    public function testAndWhere(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10')
            ->andWhere("name <> 'dave'");
        $this->assertEquals("SELECT id FROM users WHERE (id > 10) AND (name <> 'dave')", $query->getSql());
    }

    /**
     * Tests adding a "WHERE" condition object that will be "AND"ed
     */
    public function testAndWhereConditionObject(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10')
            ->andWhere($this->condition);
        $this->assertEquals('SELECT id FROM users WHERE (id > 10) AND (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    public function testBasicQuery(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users');
        $this->assertEquals('SELECT id, name FROM users', $query->getSql());
    }

    public function testBasicQueryWithAlias(): void
    {
        $query = new SelectQuery('u.id', 'u.name');
        $query->from('users', 'u');
        $this->assertEquals('SELECT u.id, u.name FROM users AS u', $query->getSql());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything(): void
    {
        $query = new SelectQuery('u.id', 'u.name', 'e.email');
        $query->addSelectExpression('p.password')
            ->from('users', 'u')
            ->innerJoin('log', 'l', 'l.userid = u.id')
            ->leftJoin('emails', 'e', 'e.userid = u.id')
            ->rightJoin('password', 'p', 'p.userid = u.id')
            ->where('u.id <> 10', 'u.name <> :notAllowedName')
            ->addNamedPlaceholderValue('notAllowedName', 'dave')
            ->andWhere('u.id <> 9')
            ->orWhere('u.name = :allowedName')
            ->groupBy('u.id', 'u.name', 'e.email')
            ->addGroupBy('p.password')
            ->having('count(*) > :minCount')
            ->andHaving('count(*) < 5')
            ->orHaving('count(*) = 2')
            ->addNamedPlaceholderValues(['allowedName' => 'brian', 'minCount' => [1, PDO::PARAM_INT]])
            ->orderBy('u.id DESC')
            ->addOrderBy('u.name ASC')
            ->limit(2)
            ->offset(1);
        $this->assertEquals(
            'SELECT u.id, u.name, e.email, p.password FROM users AS u INNER JOIN log AS l ON l.userid = u.id LEFT JOIN emails AS e ON e.userid = u.id RIGHT JOIN password AS p ON p.userid = u.id WHERE (u.id <> 10) AND (u.name <> :notAllowedName) AND (u.id <> 9) OR (u.name = :allowedName) GROUP BY u.id, u.name, e.email, p.password HAVING (count(*) > :minCount) AND (count(*) < 5) OR (count(*) = 2) ORDER BY u.id DESC, u.name ASC LIMIT 2 OFFSET 1',
            $query->getSql()
        );
        $this->assertEquals([
            'notAllowedName' => ['dave', PDO::PARAM_STR],
            'allowedName' => ['brian', PDO::PARAM_STR],
            'minCount' => [1, PDO::PARAM_INT]
        ], $query->getParameters());
    }

    /**
     * Tests adding a "GROUP BY" statement
     */
    public function testGroupBy(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->groupBy('id', 'name');
        $this->assertEquals('SELECT id, name FROM users GROUP BY id, name', $query->getSql());
    }

    /**
     * Tests adding a "HAVING" condition
     */
    public function testHaving(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having('COUNT(name) > 1');
        $this->assertEquals('SELECT name FROM users GROUP BY name HAVING (COUNT(name) > 1)', $query->getSql());
    }

    /**
     * Tests adding a "HAVING" condition object
     */
    public function testHavingConditionObject(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having($this->condition);
        $this->assertEquals('SELECT name FROM users GROUP BY name HAVING (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests adding an "INNER JOIN" statement
     */
    public function testInnerJoin(): void
    {
        $query = new SelectQuery('id');
        $query->from('users', 'u')
            ->innerJoin('log', 'l', 'l.userid = u.id');
        $this->assertEquals('SELECT id FROM users AS u INNER JOIN log AS l ON l.userid = u.id', $query->getSql());
    }

    /**
     * Tests adding a "JOIN" statement
     */
    public function testJoin(): void
    {
        $query = new SelectQuery('u.id');
        $query->from('users', 'u')
            ->join('log', 'l', 'l.userid = u.id');
        $this->assertEquals('SELECT u.id FROM users AS u INNER JOIN log AS l ON l.userid = u.id', $query->getSql());
    }

    /**
     * Tests adding an "LEFT JOIN" statement
     */
    public function testLeftJoin(): void
    {
        $query = new SelectQuery('id');
        $query->from('users', 'u')
            ->leftJoin('log', 'l', 'l.userid = u.id');
        $this->assertEquals('SELECT id FROM users AS u LEFT JOIN log AS l ON l.userid = u.id', $query->getSql());
    }

    /**
     * Tests adding a "LIMIT" statement
     */
    public function testLimit(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->limit(5);
        $this->assertEquals('SELECT id, name FROM users LIMIT 5', $query->getSql());
    }

    /**
     * Tests adding a "LIMIT" statement with a named placeholder
     */
    public function testLimitWithNamedPlaceholder(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->limit(':limit');
        $this->assertEquals('SELECT id, name FROM users LIMIT :limit', $query->getSql());
    }

    public function testMixingWhereExpessionAndObject(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10', $this->condition);
        $this->assertEquals(
            'SELECT id FROM users WHERE (id > 10) AND (c1 IN (?))',
            $query->getSql()
        );
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests adding multiple "JOIN" statements
     */
    public function testMultipleJoins(): void
    {
        $query = new SelectQuery('id');
        $query->from('users', 'u')
            ->join('log', 'l', 'l.userid = u.id')
            ->join('emails', 'e', 'e.userid = u.id');
        $this->assertEquals(
            'SELECT id FROM users AS u INNER JOIN log AS l ON l.userid = u.id INNER JOIN emails AS e ON e.userid = u.id',
            $query->getSql()
        );
    }

    /**
     * Tests adding a "OFFSET" statement
     */
    public function testOffset(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->offset(5);
        $this->assertEquals('SELECT id, name FROM users OFFSET 5', $query->getSql());
    }

    /**
     * Tests adding a "OFFSET" statement with a named placeholder
     */
    public function testOffsetWithNamedPlaceholder(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->offset(':offset');
        $this->assertEquals('SELECT id, name FROM users OFFSET :offset', $query->getSql());
    }

    /**
     * Tests adding a "HAVING" condition that will be "OR"ed
     */
    public function testOrHaving(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having('COUNT(name) > 1')
            ->orHaving('COUNT(name) < 5');
        $this->assertEquals(
            'SELECT name FROM users GROUP BY name HAVING (COUNT(name) > 1) OR (COUNT(name) < 5)',
            $query->getSql()
        );
    }

    /**
     * Tests adding a "HAVING" condition object that will be "OR"ed
     */
    public function testOrHavingConditionObject(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having('COUNT(name) > 1')
            ->orHaving($this->condition);
        $this->assertEquals(
            'SELECT name FROM users GROUP BY name HAVING (COUNT(name) > 1) OR (c1 IN (?))',
            $query->getSql()
        );
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests adding a "WHERE" condition that will be "OR"ed
     */
    public function testOrWhere(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10')
            ->orWhere("name <> 'dave'");
        $this->assertEquals("SELECT id FROM users WHERE (id > 10) OR (name <> 'dave')", $query->getSql());
    }

    /**
     * Tests adding a "WHERE" condition object that will be "OR"ed
     */
    public function testOrWhereConditionObject(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10')
            ->orWhere($this->condition);
        $this->assertEquals('SELECT id FROM users WHERE (id > 10) OR (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests adding an "ORDER BY" statement
     */
    public function testOrderBy(): void
    {
        $query = new SelectQuery('id', 'name');
        $query->from('users')
            ->orderBy('id ASC', 'name DESC');
        $this->assertEquals('SELECT id, name FROM users ORDER BY id ASC, name DESC', $query->getSql());
    }

    public function testReallyBasicQuery(): void
    {
        $query = new SelectQuery('id');
        $this->assertEquals('SELECT id', $query->getSql());
    }

    /**
     * Tests adding an "RIGHT JOIN" statement
     */
    public function testRightJoin(): void
    {
        $query = new SelectQuery('id');
        $query->from('users', 'u')
            ->rightJoin('log', 'l', 'l.userid = u.id');
        $this->assertEquals('SELECT id FROM users AS u RIGHT JOIN log AS l ON l.userid = u.id', $query->getSql());
    }

    /**
     * Tests setting a "HAVING" condition, then resetting it
     */
    public function testSettingHavingConditionWhenItWasAlreadySet(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->groupBy('name')
            ->having('COUNT(name) > 1')
            ->having('COUNT(name) < 5');
        $this->assertEquals('SELECT name FROM users GROUP BY name HAVING (COUNT(name) < 5)', $query->getSql());
    }

    /**
     * Tests setting a "WHERE" condition, then resetting it
     */
    public function testSettingWhereConditionWhenItWasAlreadySet(): void
    {
        $query = new SelectQuery('name');
        $query->from('users')
            ->where('id = 1')
            ->where('id = 2');
        $this->assertEquals('SELECT name FROM users WHERE (id = 2)', $query->getSql());
    }

    /**
     * Tests adding a "WHERE" condition
     */
    public function testWhere(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where('id > 10', "name <> 'dave'");
        $this->assertEquals("SELECT id FROM users WHERE (id > 10) AND (name <> 'dave')", $query->getSql());
    }

    /**
     * Tests adding a "WHERE" condition object
     */
    public function testWhereConditionObject(): void
    {
        $query = new SelectQuery('id');
        $query->from('users')
            ->where($this->condition, $this->condition);
        $this->assertEquals('SELECT id FROM users WHERE (c1 IN (?)) AND (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT], [1, PDO::PARAM_INT]], $query->getParameters());
    }
}
