<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the element registry
 */
namespace RDev\Console\Responses\Formatters\Elements;
use InvalidArgumentException;

class ElementCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var ElementCollection The elements to use in tests */
    private $collection = null;

    /**
     * Sets up the tests
     */
    public function setup()
    {
        $this->collection = new ElementCollection();
    }

    /**
     * Tests adding an element
     */
    public function testAddingElement()
    {
        $element = new Element("foo", new Style());
        $this->collection->add($element);
        $this->assertEquals([$element], $this->collection->getElements());
        $this->assertSame($element, $this->collection->getElement("foo"));
        $this->assertTrue($this->collection->has("foo"));
    }

    /**
     * Tests adding multiple elements
     */
    public function testAddingMultipleElements()
    {
        $element1 = new Element("foo", new Style());
        $element2 = new Element("bar", new Style());
        $this->collection->add([$element1, $element2]);
        $this->assertEquals([$element1, $element2], $this->collection->getElements());
        $this->assertSame($element1, $this->collection->getElement("foo"));
        $this->assertSame($element2, $this->collection->getElement("bar"));
        $this->assertTrue($this->collection->has("foo"));
        $this->assertTrue($this->collection->has("bar"));
    }

    /**
     * Tests getting a non-existent element
     */
    public function testGettingNonExistentElement()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->assertFalse($this->collection->has("foo"));
        $this->collection->getElement("foo");
    }
}