<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests\PostgreSql;

use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\PostgreSql\UpdateQuery;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests the update query
 */
class UpdateQueryTest extends TestCase
{
    public function testAddReturning(): void
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->returning('id')
            ->addReturning('name');
        $this->assertEquals('UPDATE users SET name = ? RETURNING id, name', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    public function testEverything(): void
    {
        $expr = new Expression('(val + ?) % ?', ['1', PDO::PARAM_INT]);
        $query = new UpdateQuery('users', 'u', ['name' => 'david']);
        $query->addColumnValues(['email' => 'bar@foo.com', 'is_val_even' => $expr])
            ->where('u.id = ?', 'emails.userid = u.id', 'emails.email = ?')
            ->orWhere('u.name = ?')
            ->andWhere('subscriptions.userid = u.id', "subscriptions.type = 'customer'")
            ->returning('u.id')
            ->addReturning('u.name')
            ->addUnnamedPlaceholderValues([[2, PDO::PARAM_INT], [18175, PDO::PARAM_INT], 'foo@bar.com', 'dave']);
        $this->assertEquals(
            "UPDATE users AS u SET name = ?, email = ?, is_val_even = (val + ?) % ? WHERE (u.id = ?) AND (emails.userid = u.id) AND (emails.email = ?) OR (u.name = ?) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer') RETURNING u.id, u.name",
            $query->getSql()
        );
        $this->assertEquals([
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
     * Tests adding a "RETURNING" clause
     */
    public function testReturning(): void
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->returning('id');
        $this->assertEquals('UPDATE users SET name = ? RETURNING id', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR]
        ], $query->getParameters());
    }
}
