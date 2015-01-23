<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the style class
 */
namespace RDev\Console\Responses\Formatters\Elements;

class StyleTest extends \PHPUnit_Framework_TestCase 
{
    /**
     * Tests adding an invalid text style
     */
    public function testAddingInvalidTextStyle()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $style = new Style();
        $style->addTextStyle("foo");
    }

    /**
     * Tests adding invalid text styles
     */
    public function testAddingInvalidTextStyles()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $style = new Style();
        $style->addTextStyles(["foo"]);
    }

    /**
     * Tests double-adding a text style
     */
    public function testDoubleAddingTextStyle()
    {
        $style = new Style();
        $style->addTextStyle("bold");
        $style->addTextStyle("bold");
        $style->addTextStyles(["underline", "underline"]);
        $this->assertEquals(["bold", "underline"], $style->getTextStyles());
    }

    /**
     * Tests formatting a string with all styles
     */
    public function testFormattingStringWithAllStyles()
    {
        $styles = new Style("red", "green", ["bold", "underline", "blink"]);
        $this->assertEquals("\033[31;42;1;4;5mfoo\033[39;49;22;24;25m", $styles->format("foo"));
    }

    /**
     * Tests formatting an empty string
     */
    public function testFormattingEmptyString()
    {
        $styles = new Style("red", "green", ["bold", "underline", "blink"]);
        $this->assertEquals("", $styles->format(""));
    }

    /**
     * Tests formatting a string without styles
     */
    public function testFormattingStringWithoutStyles()
    {
        $styles = new Style();
        $this->assertEquals("foo", $styles->format("foo"));
    }

    /**
     * Tests not passing anything in the constructor
     */
    public function testNotPassingAnythingInConstructor()
    {
        $style = new Style();
        $this->assertNull($style->getForegroundColor());
        $this->assertNull($style->getBackgroundColor());
    }

    /**
     * Tests not passing colors in the constructor
     */
    public function testPassingColorsInConstructor()
    {
        $style = new Style("blue", "green");
        $this->assertEquals("blue", $style->getForegroundColor());
        $this->assertEquals("green", $style->getBackgroundColor());
    }

    /**
     * Tests removing an invalid text style
     */
    public function testRemovingInvalidTextStyle()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $style = new Style();
        $style->addTextStyle("bold");
        $style->removeTextStyle("foo");
    }

    /**
     * Tests removing a text style
     */
    public function testRemovingTextStyle()
    {
        $style = new Style(null, null, ["bold"]);
        $style->removeTextStyle("bold");
        $this->assertEquals([], $style->getTextStyles());
    }

    /**
     * Tests setting the background color
     */
    public function testSettingBackgroundColor()
    {
        $style = new Style();
        $style->setBackgroundColor("green");
        $this->assertEquals("green", $style->getBackgroundColor());
    }

    /**
     * Tests setting the foreground color
     */
    public function testSettingForegroundColor()
    {
        $style = new Style();
        $style->setForegroundColor("blue");
        $this->assertEquals("blue", $style->getForegroundColor());
    }

    /**
     * Tests setting the background color to an invalid value
     */
    public function testSettingInvalidBackgroundColor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $style = new Style();
        $style->setBackgroundColor("foo");
    }

    /**
     * Tests setting the background color to an invalid value in the constructor
     */
    public function testSettingInvalidBackgroundColorInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new Style(null, "foo");
    }

    /**
     * Tests setting the foreground color to an invalid value
     */
    public function testSettingInvalidForegroundColor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $style = new Style();
        $style->setForegroundColor("foo");
    }

    /**
     * Tests setting the foreground color to an invalid value in the constructor
     */
    public function testSettingInvalidForegroundColorInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new Style("foo");
    }

    /**
     * Tests setting the text styles to an invalid value in the constructor
     */
    public function testSettingInvalidTextStylesInConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new Style(null, null, ["foo"]);
    }

    /**
     * Tests setting the background color to null
     */
    public function testSettingNullBackgroundColor()
    {
        $style = new Style();
        $style->setBackgroundColor(null);
        $this->assertNull($style->getBackgroundColor());
    }

    /**
     * Tests setting the foreground color to null
     */
    public function testSettingNullForegroundColor()
    {
        $style = new Style();
        $style->setForegroundColor(null);
        $this->assertNull($style->getForegroundColor());
    }
}