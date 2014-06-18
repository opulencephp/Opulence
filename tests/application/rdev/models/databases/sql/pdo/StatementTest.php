<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PDO statement
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Tests\Models\Databases\SQL\PDO\Mocks;

class StatementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests binding an invalid associative array
     */
    public function testBindingInvalidAssociativeArray()
    {
        $statement = new Mocks\Statement();
        $values = ["foo" => "bar", "id" => [1, \PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    /**
     * Tests binding an invalid indexed array
     */
    public function testBindingInvalidIndexedArray()
    {
        $statement = new Mocks\Statement();
        $values = ["bar", [1, \PDO::PARAM_INT, "this argument shouldn't be here"]];
        $this->assertFalse($statement->bindValues($values));
    }

    /**
     * Tests binding a valid associative array
     */
    public function testBindingValidAssociativeArray()
    {
        $statement = new Mocks\Statement();
        $values = ["foo" => "bar", "id" => [1, \PDO::PARAM_INT]];
        $this->assertTrue($statement->bindValues($values));
    }

    /**
     * Tests binding a valid indexed array
     */
    public function testBindingValidIndexedArray()
    {
        $statement = new Mocks\Statement();
        $values = ["bar", 1, \PDO::PARAM_INT];
        $this->assertTrue($statement->bindValues($values));
    }
} 