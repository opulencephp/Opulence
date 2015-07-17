<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests a console element
 */
namespace Opulence\Console\Responses\Formatters\Elements;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $element = new Element("foo", new Style());
        $this->assertEquals("foo", $element->getName());
    }

    /**
     * Tests setting the style through the constructor
     */
    public function testSettingStyleInConstructor()
    {
        $style = new Style("red", "black");
        $element = new Element("foo", $style);
        $this->assertSame($style, $element->getStyle());
    }

    /**
     * Tests setting the style through the setter
     */
    public function testSettingStyleInSetter()
    {
        $style1 = new Style("red", "black");
        $style2 = new Style("black", "red");
        $element = new Element("foo", $style1);
        $element->setStyle($style2);
        $this->assertSame($style2, $element->getStyle());
    }
}