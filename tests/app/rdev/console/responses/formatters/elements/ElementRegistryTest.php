<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the element registry
 */
namespace RDev\Console\Responses\Formatters\Elements;

class ElementRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ElementRegistry The element registry to use in tests */
    private $elementRegistry = null;

    /**
     * Sets up the tests
     */
    public function setup()
    {
        $this->elementRegistry = new ElementRegistry();
    }

    /**
     * Tests getting a non-existent element
     */
    public function testGettingNonExistentElement()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->assertFalse($this->elementRegistry->isRegistered("foo"));
        $this->elementRegistry->getElement("foo");
    }

    /**
     * Tests registering an element
     */
    public function testRegisteringElement()
    {
        $element = new Element("foo", new Style());
        $this->elementRegistry->registerElement($element);
        $this->assertEquals([$element], $this->elementRegistry->getElements());
        $this->assertSame($element, $this->elementRegistry->getElement("foo"));
        $this->assertTrue($this->elementRegistry->isRegistered("foo"));
    }
}