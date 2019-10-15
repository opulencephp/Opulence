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

use Opulence\QueryBuilders\MySql\DeleteQuery;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests the delete query
 */
class DeleteQueryTest extends TestCase
{
    /**
     * Tests all the methods in a single, complicated query
     */
    public function testEverything(): void
    {
        $query = new DeleteQuery('users', 'u');
        $query->where('u.id = :userId')
            ->andWhere('u.name = :name')
            ->orWhere('u.id = 10')
            ->addNamedPlaceholderValues(['userId' => [18175, PDO::PARAM_INT]])
            ->addNamedPlaceholderValue('name', 'dave')
            ->limit(1);
        $this->assertEquals(
            'DELETE FROM users AS u WHERE (u.id = :userId) AND (u.name = :name) OR (u.id = 10) LIMIT 1',
            $query->getSql()
        );
        $this->assertEquals([
            'userId' => [18175, PDO::PARAM_INT],
            'name' => ['dave', PDO::PARAM_STR]
        ], $query->getParameters());
    }

    public function testLimit(): void
    {
        $query = new DeleteQuery('users');
        $query->limit(1);
        $this->assertEquals('DELETE FROM users LIMIT 1', $query->getSql());
    }

    public function testLimitWithNamedPlaceholder(): void
    {
        $query = new DeleteQuery('users');
        $query->limit(':limit');
        $this->assertEquals('DELETE FROM users LIMIT :limit', $query->getSql());
    }
}
