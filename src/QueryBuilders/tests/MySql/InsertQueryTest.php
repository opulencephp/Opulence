<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests\MySql;

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
    public function testAddingColumnsToUpdate(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $query->update(['name' => 'dave'])
            ->addUpdateColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals(
            'INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?',
            $query->getSql()
        );
        $this->assertEquals([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    public function testBasicQuery(): void
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
    public function testEverything(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $query->update(['name' => 'dave'])
            ->addUpdateColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals(
            'INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?',
            $query->getSql()
        );
        $this->assertEquals([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests the INSERT/UPDATE ability
     */
    public function testInsertUpdate(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $query->update(['name' => 'dave', 'email' => 'foo@bar.com']);
        $this->assertEquals(
            'INSERT INTO users (name, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?, email = ?',
            $query->getSql()
        );
        $this->assertEquals([
            ['dave', PDO::PARAM_STR],
            ['foo@bar.com', PDO::PARAM_STR]
        ], $query->getParameters());
    }
}
