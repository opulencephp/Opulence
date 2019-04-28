<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\QueryBuilders\Tests;

use Opulence\QueryBuilders\InsertQuery;

/**
 * Tests the insert query
 */
class InsertQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding more columns to the query
     */
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

    /**
     * Tests a basic query
     */
    public function testBasicQuery(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave', 'email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?)', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything(): void
    {
        $query = new InsertQuery('users', ['name' => 'dave']);
        $query->addColumnValues(['email' => 'foo@bar.com']);
        $this->assertEquals('INSERT INTO users (name, email) VALUES (?, ?)', $query->getSql());
        $this->assertEquals([
            ['dave', \PDO::PARAM_STR],
            ['foo@bar.com', \PDO::PARAM_STR]
        ], $query->getParameters());
    }
}
