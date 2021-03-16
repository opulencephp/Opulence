<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests;

use Opulence\QueryBuilders\Conditions\ICondition;
use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\UpdateQuery;
use PDO;

/**
 * Tests the update query
 */
class UpdateQueryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ICondition The condition to use in tests */
    private $condition = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
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
     * Tests adding more columns
     */
    public function testAddingMoreColumns()
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->addColumnValues(['email' => 'bar@foo.com']);
        $this->assertEquals('UPDATE users SET name = ?, email = ?', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR],
            ['bar@foo.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests a basic query
     */
    public function testBasicQuery()
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $this->assertEquals('UPDATE users SET name = ?', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests a query with a simple UpsertExpression
     */
    public function testSimpleExpression()
    {
        $query = new UpdateQuery('users', '', ['valid_until' => new Expression('NOW()')]);
        $this->assertEquals('UPDATE users SET valid_until = NOW()', $query->getSql());
        $this->assertEquals([], $query->getParameters());
    }

    /**
     * Tests a query with a complex UpsertExpression
     */
    public function testComplexExpression()
    {
        $query = new UpdateQuery('users', '',
            ['is_val_even' => new Expression('(val + ?) % ?', ['1', PDO::PARAM_INT], [2, PDO::PARAM_INT])]);
        $this->assertEquals('UPDATE users SET is_val_even = (val + ?) % ?', $query->getSql());
        $this->assertEquals([
            ['1', PDO::PARAM_INT],
            [2, PDO::PARAM_INT],
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $expr = new Expression("(val + ?) % ?", ['1', PDO::PARAM_INT]);
        $query = new UpdateQuery('users', 'u', ['name' => 'david']);
        $query->addColumnValues(['email' => 'bar@foo.com', 'is_val_even' => $expr])
            ->where('u.id = ?', 'emails.userid = u.id', 'emails.email = ?')
            ->orWhere('u.name = ?')
            ->andWhere('subscriptions.userid = u.id', "subscriptions.type = 'customer'")
            ->addUnnamedPlaceholderValues([[2, PDO::PARAM_INT], [18175, PDO::PARAM_INT], 'foo@bar.com', 'dave']);
        $this->assertEquals("UPDATE users AS u SET name = ?, email = ?, is_val_even = (val + ?) % ? WHERE (u.id = ?) AND (emails.userid = u.id) AND (emails.email = ?) OR (u.name = ?) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer')",
            $query->getSql());
        $this->assertSame([
            ['david', PDO::PARAM_STR],
            ['bar@foo.com', PDO::PARAM_STR],
            ['1', PDO::PARAM_INT],
            [2, PDO::PARAM_INT],
            [18175, PDO::PARAM_INT],
            ['foo@bar.com', PDO::PARAM_STR],
            ['dave', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests adding a "WHERE" clause
     */
    public function testWhere()
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->where('id = ?')
            ->addUnnamedPlaceholderValue(18175, PDO::PARAM_INT);
        $this->assertEquals('UPDATE users SET name = ? WHERE (id = ?)', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR],
            [18175, PDO::PARAM_INT]
        ], $query->getParameters());
    }

    /**
     * Tests adding a "WHERE" clause condition object
     */
    public function testWhereConditionObject()
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->where($this->condition)
            ->addUnnamedPlaceholderValue(18175, PDO::PARAM_INT);
        $this->assertEquals('UPDATE users SET name = ? WHERE (c1 IN (?))', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR],
            [1, PDO::PARAM_INT],
            [18175, PDO::PARAM_INT]
        ], $query->getParameters());
    }
}
