<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PDO statement
 */
namespace Opulence\Databases\PDO;

use PDO;
use Opulence\Tests\Databases\SQL\PDO\Mocks\Statement;

class StatementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests binding an invalid associative array
     */
    public function testBindingInvalidAssociativeArray()
    {
        $statement = new Statement();
        $values = ["foo" => "bar", "id" => [1, PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    /**
     * Tests binding an invalid indexed array
     */
    public function testBindingInvalidIndexedArray()
    {
        $statement = new Statement();
        $values = ["bar", [1, PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    /**
     * Tests binding a valid associative array
     */
    public function testBindingValidAssociativeArray()
    {
        $statement = new Statement();
        $values = ["foo" => "bar", "id" => [1, PDO::PARAM_INT]];
        $this->assertTrue($statement->bindValues($values));
    }

    /**
     * Tests binding a valid indexed array
     */
    public function testBindingValidIndexedArray()
    {
        $statement = new Statement();
        $values = ["bar", 1, PDO::PARAM_INT];
        $this->assertTrue($statement->bindValues($values));
    }
} 