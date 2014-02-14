<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query class
 */
namespace RamODev\Databases\RDBMS\QueryBuilders;

require_once(__DIR__ . "/../../../../databases/rdbms/querybuilders/Query.php");

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Query The query object stub */
    private $query = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->query = $this->getMockForAbstractClass("\\RamODev\\Databases\\RDBMS\\QueryBuilders\\Query");
    }

    /**
     * Tests adding a named placeholder
     */
    public function testAddingNamedPlaceholder()
    {
        $this->query->addNamedPlaceholderValue("userID", 18175);
        $this->assertEquals(array("userID" => 18175), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when we add a named placeholder after an unnamed one
     */
    public function testAddingNamedPlaceholderAfterAddingUnnamedPlaceholder()
    {
        $this->setExpectedException("RamODev\\Databases\\RDBMS\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addUnnamedPlaceholderValue("dave")
            ->addNamedPlaceholderValue("id", 18175);
    }

    /**
     * Tests adding an unnamed placeholder
     */
    public function testAddingUnnamedPlaceholder()
    {
        $this->query->addUnnamedPlaceholderValue(18175);
        $this->assertEquals(array(18175), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when we add an unnamed placeholder after a named one
     */
    public function testAddingUnnamedPlaceholderAfterAddingNamedPlaceholder()
    {
        $this->setExpectedException("RamODev\\Databases\\RDBMS\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValue("id", 18175)
            ->addUnnamedPlaceholderValue("dave");
    }
} 