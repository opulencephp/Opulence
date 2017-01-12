<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\PostgreSql;

use PDO;

/**
 * Tests the update query
 */
class UpdateQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding to a "RETURNING" clause
     */
    public function testAddReturning()
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->returning('id')
            ->addReturning('name');
        $this->assertEquals('UPDATE users SET name = ? RETURNING id, name', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new UpdateQuery('users', 'u', ['name' => 'david']);
        $query->addColumnValues(['email' => 'bar@foo.com'])
            ->where('u.id = ?', 'emails.userid = u.id', 'emails.email = ?')
            ->orWhere('u.name = ?')
            ->andWhere('subscriptions.userid = u.id', "subscriptions.type = 'customer'")
            ->returning('u.id')
            ->addReturning('u.name')
            ->addUnnamedPlaceholderValues([[18175, PDO::PARAM_INT], 'foo@bar.com', 'dave']);
        $this->assertEquals("UPDATE users AS u SET name = ?, email = ? WHERE (u.id = ?) AND (emails.userid = u.id) AND (emails.email = ?) OR (u.name = ?) AND (subscriptions.userid = u.id) AND (subscriptions.type = 'customer') RETURNING u.id, u.name",
            $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR],
            ['bar@foo.com', PDO::PARAM_STR],
            [18175, PDO::PARAM_INT],
            ['foo@bar.com', PDO::PARAM_STR],
            ['dave', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests adding a "RETURNING" clause
     */
    public function testReturning()
    {
        $query = new UpdateQuery('users', '', ['name' => 'david']);
        $query->returning('id');
        $this->assertEquals('UPDATE users SET name = ? RETURNING id', $query->getSql());
        $this->assertEquals([
            ['david', PDO::PARAM_STR]
        ], $query->getParameters());
    }
}
