<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view
 */
namespace Opulence\Views;
use InvalidArgumentException;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    /** @var View The view to use in the tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->view = new View();
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertNull($this->view->getVar("foo"));
    }

    /**
     * Tests getting a var
     */
    public function testGettingVar()
    {
        $this->view->setVar("foo", "bar");
        $this->assertEquals("bar", $this->view->getVar("foo"));
    }

    /**
     * Tests getting the bars
     */
    public function testGettingVars()
    {
        $this->view->setVar("foo", "bar");
        $this->assertEquals(["foo" => "bar"], $this->view->getVars());
    }

    /**
     * Tests not setting the contents in the constructor
     */
    public function testNotSettingContentsInConstructor()
    {
        $this->assertEmpty($this->view->getContents());
    }

    /**
     * Tests setting the contents
     */
    public function testSettingContents()
    {
        $this->view->setContents("blah");
        $this->assertEquals("blah", $this->view->getContents());
    }

    /**
     * Tests setting the contents in the constructor
     */
    public function testSettingContentsInConstructor()
    {
        $view = new View("foo", "bar");
        $this->assertEquals("bar", $view->getContents());
    }

    /**
     * Tests setting the contents to a non-string
     */
    public function testSettingContentsToNonString()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->view->setContents(["Not a string"]);
    }

    /**
     * Tests setting multiple variables in a view
     */
    public function testSettingMultipleVariables()
    {
        $this->view->setVars(["foo" => "bar", "abc" => ["xyz"]]);
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(["foo" => "bar", "abc" => ["xyz"]], $vars);
    }

    /**
     * Tests setting the path in the constructor
     */
    public function testSettingPathInConstructor()
    {
        $view = new View("foo");
        $this->assertEquals("foo", $view->getPath());
    }

    /**
     * Tests setting the path in the setter
     */
    public function testSettingPathInSetter()
    {
        $this->view->setPath("foo");
        $this->assertEquals("foo", $this->view->getPath());
    }

    /**
     * Tests setting a variable in a view
     */
    public function testSettingSingleVariable()
    {
        $this->view->setVar("foo", "bar");
        $reflectionObject = new \ReflectionObject($this->view);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->view);
        $this->assertEquals(["foo" => "bar"], $vars);
    }
}