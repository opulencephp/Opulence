<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests\MySql;

use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\MySql\InsertQuery;
use PDO;

/**
 * Tests the insert query
 */
class InsertQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding columns to the update portion of an INSERT/UPDATE
     */
    public function testAddingColumnsToUpdate()
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $query->update(['name' => 'dave'])
            ->addUpdateColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?',
            $query->getSql());
        $this->assertEquals([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests a basic query
     */
    public function testBasicQuery()
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?)', $query->getSql());
        $this->assertEquals([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new InsertQuery('users',
            [
                'name' => 'dave',
                'email' => 'foo@bar.com',
                'is_val_even' => new Expression('(val + ?) % ?', ['1', \PDO::PARAM_INT], [2, \PDO::PARAM_INT])
            ]);
        $query->update(['name' => 'dave'])
            ->addUpdateColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email, is_val_even) VALUES (?, ?, (val + ?) % ?) ON DUPLICATE KEY UPDATE name = ?, email = ?',
            $query->getSql());
        $this->assertSame([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR],
            ['1', \PDO::PARAM_INT],
            [2, \PDO::PARAM_INT],
        ], $query->getParameters());
    }

    /**
     * Tests the INSERT/UPDATE ability
     */
    public function testInsertUpdate()
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $query->update(['name' => 'dave', 'email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?',
            $query->getSql());
        $this->assertEquals([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }
}
