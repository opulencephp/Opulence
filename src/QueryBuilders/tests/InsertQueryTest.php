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

use Opulence\QueryBuilders\Expression;
use Opulence\QueryBuilders\InsertQuery;
use PHPUnit\Framework\TestCase;

/**
 * Tests the insert query
 */
class InsertQueryTest extends TestCase
{
    public function testAddingMoreColumns(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->addColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?)', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    public function testBasicQuery(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?)', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    public function testComplexExpression(): void
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

    public function testEverything(): void
    {
        $expr = new Expression('(val + ?) % ?', ['1', \PDO::PARAM_INT]);
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->addColumnValues(['email' => 'foo@bar.com', 'is_val_even' => $expr])
            ->addUnnamedPlaceholderValues([[2, \PDO::PARAM_INT]]);
        $this->assertEquals('INSERT INTO users (name, email, is_val_even) VALUES (?, ?, (val + ?) % ?)', $query->getSql());
        $this->assertSame([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR],
            ['1', \PDO::PARAM_INT],
            [2, \PDO::PARAM_INT]
        ], $query->getParameters());
    }

    public function testSimpleExpression(): void
    {
        $query = new InsertQuery('users',
            ['name' => 'dave', 'email' => 'foo@bar.com', 'valid_until' => new Expression('NOW()')]);
        $this->assertEquals('INSERT INTO users (name, email, valid_until) VALUES (?, ?, NOW())', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR],
        ], $query->getParameters());
    }
}
