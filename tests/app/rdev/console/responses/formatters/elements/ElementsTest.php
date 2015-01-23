<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the element registry
 */
namespace RDev\Console\Responses\Formatters\Elements;

class ElementsTest extends \PHPUnit_Framework_TestCase
{
    /** @var Elements The elements to use in tests */
    private $elements = null;

    /**
     * Sets up the tests
     */
    public function setup()
    {
        $this->elements = new Elements();
    }

    /**
     * Tests adding an element
     */
    public function testAddingElement()
    {
        $element = new Element("foo", new Style());
        $this->elements->add($element);
        $this->assertEquals([$element], $this->elements->getElements());
        $this->assertSame($element, $this->elements->getElement("foo"));
        $this->assertTrue($this->elements->has("foo"));
    }

    /**
     * Tests adding multiple elements
     */
    public function testAddingMultipleElements()
    {
        $element1 = new Element("foo", new Style());
        $element2 = new Element("bar", new Style());
        $this->elements->add([$element1, $element2]);
        $this->assertEquals([$element1, $element2], $this->elements->getElements());
        $this->assertSame($element1, $this->elements->getElement("foo"));
        $this->assertSame($element2, $this->elements->getElement("bar"));
        $this->assertTrue($this->elements->has("foo"));
        $this->assertTrue($this->elements->has("bar"));
    }

    /**
     * Tests getting a non-existent element
     */
    public function testGettingNonExistentElement()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->assertFalse($this->elements->has("foo"));
        $this->elements->getElement("foo");
    }
}