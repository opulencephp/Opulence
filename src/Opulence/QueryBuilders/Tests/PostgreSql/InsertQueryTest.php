<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests\PostgreSql;

use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\PostgreSql\InsertQuery;
use PDO;

/**
 * Tests the insert query
 */
class InsertQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding a column to return
     */
    public function testAddReturning()
    {
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->returning('id')
            ->addReturning('name');
        $this->assertEquals('INSERT INTO users (name) VALUES (?) RETURNING id, name', $query->getSql());
        $this->assertEquals([
            ['dave', PDO::PARAM_STR]
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
            ->returning('id')
            ->addReturning('name')
            ->addUnnamedPlaceholderValues([[2, \PDO::PARAM_INT]]);
        $this->assertEquals('INSERT INTO users (name, email, is_val_even) VALUES (?, ?, (val + ?) % ?) RETURNING id, name',
            $query->getSql());
        $this->assertSame([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR],
            ['1', \PDO::PARAM_INT],
            [2, \PDO::PARAM_INT],
        ], $query->getParameters());
    }

    /**
     * Tests returning a column value
     */
    public function testReturning()
    {
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->returning('id', 'name');
        $this->assertEquals('INSERT INTO users (name) VALUES (?) RETURNING id, name', $query->getSql());
        $this->assertEquals([
            ['dave', PDO::PARAM_STR]
        ], $query->getParameters());
    }
}
