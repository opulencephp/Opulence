<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\QueryBuilders\Tests\MySql;

use Opulence\QueryBuilders\MySql\DeleteQuery;
use PDO;

/**
 * Tests the delete query
 */
class DeleteQueryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything()
    {
        $query = new DeleteQuery('users', 'u');
        $query->where('u.id = :userId')
            ->andWhere('u.name = :name')
            ->orWhere('u.id = 10')
            ->addNamedPlaceholderValues(['userId' => [18175, PDO::PARAM_INT]])
            ->addNamedPlaceholderValue('name', 'dave')
            ->limit(1);
        $this->assertEquals('DELETE FROM users AS u WHERE (u.id = :userId) AND (u.name = :name) OR (u.id = 10) LIMIT 1',
            $query->getSql());
        $this->assertEquals([
            'userId' => [18175, PDO::PARAM_INT],
            'name' => ['dave', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    /**
     * Tests the limit clause
     */
    public function testLimit()
    {
        $query = new DeleteQuery('users');
        $query->limit(1);
        $this->assertEquals('DELETE FROM users LIMIT 1', $query->getSql());
    }

    /**
     * Tests the limit clause with a named placeholder
     */
    public function testLimitWithNamedPlaceholder()
    {
        $query = new DeleteQuery('users');
        $query->limit(':limit');
        $this->assertEquals('DELETE FROM users LIMIT :limit', $query->getSql());
    }
}
