<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the query class
 */
namespace RamODev\Application\Shared\Databases\SQL\QueryBuilders;

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Query The query object stub */
    private $query = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->query = $this->getMockForAbstractClass("\\RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Query");
    }

    /**
     * Tests adding a named placeholder
     */
    public function testAddingNamedPlaceholder()
    {
        $this->query->addNamedPlaceholderValue("userId", 18175);
        $this->assertEquals(array("userId" => 18175), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when adding a named placeholder after an unnamed one
     */
    public function testAddingNamedPlaceholderAfterAddingUnnamedPlaceholder()
    {
        $this->setExpectedException("RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
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
     * Tests the exception that should be thrown when adding an unnamed placeholder after a named one
     */
    public function testAddingUnnamedPlaceholderAfterAddingNamedPlaceholder()
    {
        $this->setExpectedException("RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValue("id", 18175)
            ->addUnnamedPlaceholderValue("dave");
    }

    /**
     * Tests removing a named placeholder
     */
    public function testRemovingNamedPlaceholder()
    {
        $key = "foo";
        $this->query = $this->getMockForAbstractClass("\\RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Query");
        $this->query->addNamedPlaceholderValue($key, "bar");
        $this->query->removeNamedPlaceHolder($key);
        $this->assertFalse(array_key_exists($key, $this->query->getParameters()));
    }

    /**
     * Tests removing a named placeholder when using unnamed placeholders
     */
    public function testRemovingNamedPlaceholderWhenUsingUnnamedPlaceholders()
    {
        $this->setExpectedException("RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addUnnamedPlaceholderValue("foo");
        $this->query->removeNamedPlaceHolder("bar");
    }

    /**
     * Tests removing an unnamed placeholder
     */
    public function testRemovingUnnamedPlaceholder()
    {
        $this->query = $this->getMockForAbstractClass("\\RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Query");
        $this->query->addUnnamedPlaceholderValue("foo")
            ->addUnnamedPlaceholderValue("bar")
            ->addUnnamedPlaceholderValue("xyz");
        $this->query->removeUnnamedPlaceHolder(1);
        $this->assertFalse(in_array("bar", $this->query->getParameters()));
    }

    /**
     * Tests removing an unnamed placeholder when using named placeholders
     */
    public function testRemovingUnnamedPlaceholderWhenUsingNamedPlaceholders()
    {
        $this->setExpectedException("RamODev\\Application\\Shared\\Databases\\SQL\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValue("foo", "bar");
        $this->query->removeUnnamedPlaceHolder(0);
    }
} 