<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Tests\Adapters\Pdo;

use Opulence\Databases\Tests\Adapters\Pdo\Mocks\Statement;
use PDO;

/**
 * Tests the PDO statement
 */
class StatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests binding an invalid associative array
     */
    public function testBindingInvalidAssociativeArray()
    {
        $statement = new Statement();
        $values = ['foo' => 'bar', 'id' => [1, PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    /**
     * Tests binding an invalid indexed array
     */
    public function testBindingInvalidIndexedArray()
    {
        $statement = new Statement();
        $values = ['bar', [1, PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    /**
     * Tests binding a valid associative array
     */
    public function testBindingValidAssociativeArray()
    {
        $statement = new Statement();
        $values = ['foo' => 'bar', 'id' => [1, PDO::PARAM_INT]];
        $this->assertTrue($statement->bindValues($values));
    }

    /**
     * Tests binding a valid indexed array
     */
    public function testBindingValidIndexedArray()
    {
        $statement = new Statement();
        $values = ['bar', 1, PDO::PARAM_INT];
        $this->assertTrue($statement->bindValues($values));
    }
}
