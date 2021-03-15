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
use Opulence\QueryBuilders\DeleteQuery;
use PDO;

/**
 * Tests the delete query
 */
class DeleteQueryTest extends \PHPUnit\Framework\TestCase
{
    /** @var ICondition The condition to use in tests */
    private $condition = null;

    /**
     * Sets up the tests
     */
    public function setUp()
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
     * Tests adding a "USING" expression
     */
    public function testAddingUsing()
    {
        $query = new DeleteQuery('users');
        $query->using('emails')
            ->addUsing('subscriptions')
            ->where("users.id = emails.userid AND emails.email = 'foo@bar.com'")
            ->orWhere("subscriptions.userid = users.id AND subscriptions.type = 'customer'");
        $this->assertEquals("DELETE FROM users USING emails, subscriptions WHERE (users.id = emails.userid AND emails.email = 'foo@bar.com') OR (subscriptions.userid = users.id AND subscriptions.type = 'customer')",
            $query->getSql());
    }

    /**
     * Tests adding an "AND" where condition
     */
    public function testAndWhere()
    {
        $query = new DeleteQuery('users');
        $query->where('id = 1')
            ->andWhere("name = 'dave'");
        $this->assertEquals("DELETE FROM users WHERE (id = 1) AND (name = 'dave')", $query->getSql());
    }

    /**
     * Tests adding an "AND" where condition object
     */
    public function testAndWhereConditionObject()
    {
        $query = new DeleteQuery('users');
        $query->where('id = 1')
            ->andWhere($this->condition);
        $this->assertEquals('DELETE FROM users WHERE (id = 1) AND (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests the most basic query we can run
     */
    public function testBasicQuery()
    {
        $query = new DeleteQuery('users');
        $this->assertEquals('DELETE FROM users', $query->getSql());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new DeleteQuery('users', 'u');
        $query->using('emails')
            ->addUsing('subscriptions')
            ->where('u.id = :userId', 'emails.userid = u.id', 'emails.email = :email')
            ->orWhere('u.name = :name')
            ->andWhere('subscriptions.userid = u.id', "subscriptions.type = 'customer'")
            ->addNamedPlaceholderValues(['userId' => 18175, 'email' => 'foo@bar.com', 'name' => 'dave']);
        $this->assertEquals("DELETE FROM users AS u USING emails, subscriptions WHERE (u.id = :userId) AND (emails.userid = u.id) AND (emails.email = :email) OR (u.name = :name) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer')",
            $query->getSql());
    }

    /**
     * Tests adding an "OR" where condition
     */
    public function testOrWhere()
    {
        $query = new DeleteQuery('users');
        $query->where('id = 1')
            ->orWhere("name = 'dave'");
        $this->assertEquals("DELETE FROM users WHERE (id = 1) OR (name = 'dave')", $query->getSql());
    }

    /**
     * Tests adding an "OR" where condition
     */
    public function testOrWhereConditionObject()
    {
        $query = new DeleteQuery('users');
        $query->where('id = 1')
            ->orWhere($this->condition);
        $this->assertEquals('DELETE FROM users WHERE (id = 1) OR (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }

    /**
     * Tests using an alias on the table name
     */
    public function testTableAlias()
    {
        $query = new DeleteQuery('users', 'u');
        $this->assertEquals('DELETE FROM users AS u', $query->getSql());
    }

    /**
     * Tests the "USING" expression
     */
    public function testUsing()
    {
        $query = new DeleteQuery('users');
        $query->using('emails')
            ->where("users.id = emails.userid AND emails.email = 'foo@bar.com'");
        $this->assertEquals("DELETE FROM users USING emails WHERE (users.id = emails.userid AND emails.email = 'foo@bar.com')",
            $query->getSql());
    }

    /**
     * Tests adding a simple where clause
     */
    public function testWhere()
    {
        $query = new DeleteQuery('users');
        $query->where('id = 1');
        $this->assertEquals('DELETE FROM users WHERE (id = 1)', $query->getSql());
    }

    /**
     * Tests adding a simple where clause with a condition object
     */
    public function testWhereConditionObject()
    {
        $query = new DeleteQuery('users');
        $query->where($this->condition);
        $this->assertEquals('DELETE FROM users WHERE (c1 IN (?))', $query->getSql());
        $this->assertEquals([[1, PDO::PARAM_INT]], $query->getParameters());
    }
}
