<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests;

use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\InsertQuery;

/**
 * Tests the insert query
 */
class InsertQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding more columns to the query
     */
    public function testAddingMoreColumns()
    {
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->addColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?)', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR],
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
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests a query with a simple UpsertExpression
     */
    public function testSimpleExpression()
    {
        $query = new InsertQuery('users',
            ['name' => 'dave', 'email' => 'foo@bar.com', 'valid_until' => new Expression('NOW()')]);
        $this->assertEquals('INSERT INTO users (name, email, valid_until) VALUES (?, ?, NOW())', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR],
        ], $query->getParameters());
    }

    /**
     * Tests a query with a complex UpsertExpression
     */
    public function testComplexExpression()
    {
        $query = new InsertQuery('users',
            [
                'name' => 'dave',
                'email' => 'foo@bar.com',
                'is_val_even' => new Expression('(val + ?) % ?', ['1', \PDO::PARAM_INT], [2, \PDO::PARAM_INT])
            ]);
        $this->assertEquals('INSERT INTO users (name, email, is_val_even) VALUES (?, ?, (val + ?) % ?)',
            $query->getSql());
        $this->assertSame([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR],
            ['1', \PDO::PARAM_INT],
            [2, \PDO::PARAM_INT],
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $expr = new Expression("(val + ?) % ?", ['1', \PDO::PARAM_INT]);
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->addColumnValues(['email' => 'foo@bar.com', 'is_val_even' => $expr])
            ->addUnnamedPlaceholderValues([[2, \PDO::PARAM_INT]]);
        $this->assertEquals('INSERT INTO users (name, email, is_val_even) VALUES (?, ?, (val + ?) % ?)', $query->getSql());
        $this->assertSame([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR],
            ['1', \PDO::PARAM_INT],
            [2, \PDO::PARAM_INT],
        ], $query->getParameters());
    }
}
