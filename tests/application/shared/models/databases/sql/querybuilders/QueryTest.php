<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query class
 */
namespace RDev\Application\Shared\Models\Databases\SQL\QueryBuilders;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Query The query object stub */
    private $query = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->query = $this->getMockForAbstractClass("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Query");
    }

    /**
     * Tests adding a named placeholder
     */
    public function testAddingNamedPlaceholder()
    {
        $this->query->addNamedPlaceholderValue("name", "foo");
        $this->assertEquals(array(
            "name" => array("foo", \PDO::PARAM_STR)
        ), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when adding a named placeholder after an unnamed one
     */
    public function testAddingNamedPlaceholderAfterAddingUnnamedPlaceholder()
    {
        $this->setExpectedException("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addUnnamedPlaceholderValue("dave")
            ->addNamedPlaceholderValue("id", 18175);
    }

    /**
     * Tests adding a named placeholder with data type
     */
    public function testAddingNamedPlaceholderWithDataType()
    {
        $this->query->addNamedPlaceholderValue("userId", 18175, \PDO::PARAM_INT);
        $this->assertEquals(array(
            "userId" => array(18175, \PDO::PARAM_INT)
        ), $this->query->getParameters());
    }

    /**
     * Tests adding an array with the named value with the incorrect number of arguments
     */
    public function testAddingNamedPlaceholderWithIncorrectArrayValueCount()
    {
        $this->setExpectedException("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValues(array("foo" => array("bar")));
    }

    /**
     * Tests adding an unnamed placeholder
     */
    public function testAddingUnnamedPlaceholder()
    {
        $this->query->addUnnamedPlaceholderValue("foo");
        $this->assertEquals(array(
            array("foo", \PDO::PARAM_STR)
        ), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when adding an unnamed placeholder after a named one
     */
    public function testAddingUnnamedPlaceholderAfterAddingNamedPlaceholder()
    {
        $this->setExpectedException("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValue("id", 18175)
            ->addUnnamedPlaceholderValue("dave");
    }

    /**
     * Tests adding an unnamed placeholder with data type
     */
    public function testAddingUnnamedPlaceholderWithDataType()
    {
        $this->query->addUnnamedPlaceholderValue(18175, \PDO::PARAM_INT);
        $this->assertEquals(array(
            array(18175, \PDO::PARAM_INT)
        ), $this->query->getParameters());
    }

    /**
     * Tests adding an array with the unnamed value with the incorrect number of arguments
     */
    public function testAddingUnnamedPlaceholderWithIncorrectArrayValueCount()
    {
        $this->setExpectedException("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addUnnamedPlaceholderValues(array(array("bar")));
    }

    /**
     * Tests removing a named placeholder
     */
    public function testRemovingNamedPlaceholder()
    {
        $key = "foo";
        $this->query = $this->getMockForAbstractClass("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Query");
        $this->query->addNamedPlaceholderValue($key, "bar");
        $this->query->removeNamedPlaceHolder($key);
        $this->assertFalse(array_key_exists($key, $this->query->getParameters()));
    }

    /**
     * Tests removing a named placeholder when using unnamed placeholders
     */
    public function testRemovingNamedPlaceholderWhenUsingUnnamedPlaceholders()
    {
        $this->setExpectedException("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addUnnamedPlaceholderValue("foo");
        $this->query->removeNamedPlaceHolder("bar");
    }

    /**
     * Tests removing an unnamed placeholder
     */
    public function testRemovingUnnamedPlaceholder()
    {
        $this->query = $this->getMockForAbstractClass("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Query");
        $this->query->addUnnamedPlaceholderValue("foo")
            ->addUnnamedPlaceholderValue("bar")
            ->addUnnamedPlaceholderValue("xyz");
        $this->query->removeUnnamedPlaceHolder(1);
        $parameters = $this->query->getParameters();
        $fooFound = false;

        foreach($parameters as $parameterData)
        {
            if($parameterData[0] == "bar")
            {
                $fooFound = true;
                break;
            }
        }

        $this->assertFalse($fooFound);
    }

    /**
     * Tests removing an unnamed placeholder when using named placeholders
     */
    public function testRemovingUnnamedPlaceholderWhenUsingNamedPlaceholders()
    {
        $this->setExpectedException("RDev\\Application\\Shared\\Models\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValue("foo", "bar");
        $this->query->removeUnnamedPlaceHolder(0);
    }
} 