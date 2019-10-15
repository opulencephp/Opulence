<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\tests;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use Opulence\QueryBuilders\Conditions\ICondition;
use Opulence\QueryBuilders\UpdateQuery;
use PDO;

/**
 * Tests the update query
 */
class UpdateQueryTest extends TestCase
{
    /** @var ICondition|MockObject The condition to use in tests */
    private ICondition $condition;

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

    public function testAddingMoreColumns(): void
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->addColumnValues(['email' => 'bar@foo.com']);
        $this->assertEquals('UPDATE users SET name = ?, email = ?', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR],
            ['bar@foo.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    public function testBasicQuery(): void
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $this->assertEquals('UPDATE users SET name = ?', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything(): void
    {
        $query = new UpdateQuery('users', 'u', ['name' => 'david']);
        $query->addColumnValues(['email' => 'bar@foo.com'])
            ->where('u.id = ?', 'emails.userid = u.id', 'emails.email = ?')
            ->orWhere('u.name = ?')
            ->andWhere('subscriptions.userid = u.id', "subscriptions.type = 'customer'")
            ->addUnnamedPlaceholderValues([[18175, PDO::PARAM_INT], 'foo@bar.com', 'dave']);
        $this->assertEquals(
            "UPDATE users AS u SET name = ?, email = ? WHERE (u.id = ?) AND (emails.userid = u.id) AND (emails.email = ?) OR (u.name = ?) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer')",
            $query->getSql()
        );
        $this->assertEquals([
            ['david', PDO::PARAM_STR],
            ['bar@foo.com', PDO::PARAM_STR],
            [18175, PDO::PARAM_INT],
            ['foo@bar.com', PDO::PARAM_STR],
            ['dave', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests adding a "WHERE" clause
     */
    public function testWhere(): void
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
    public function testWhereConditionObject(): void
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
