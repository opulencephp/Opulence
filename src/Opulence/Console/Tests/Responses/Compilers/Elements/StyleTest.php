<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers\Elements;

use InvalidArgumentException;
use Opulence\Console\Responses\Compilers\Elements\Colors;
use Opulence\Console\Responses\Compilers\Elements\Style;
use Opulence\Console\Responses\Compilers\Elements\TextStyles;

/**
 * Tests the style class
 */
class StyleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests adding an invalid text style
     */
    public function testAddingInvalidTextStyle()
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->addTextStyle('foo');
    }

    /**
     * Tests adding invalid text styles
     */
    public function testAddingInvalidTextStyles()
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->addTextStyles(['foo']);
    }

    /**
     * Tests double-adding a text style
     */
    public function testDoubleAddingTextStyle()
    {
        $style = new Style();
        $style->addTextStyle(TextStyles::BOLD);
        $style->addTextStyle(TextStyles::BOLD);
        $style->addTextStyles([TextStyles::UNDERLINE, TextStyles::UNDERLINE]);
        $this->assertEquals([TextStyles::BOLD, TextStyles::UNDERLINE], $style->getTextStyles());
    }

    /**
     * Tests formatting an empty string
     */
    public function testFormattingEmptyString()
    {
        $styles = new Style(Colors::RED, Colors::GREEN, [TextStyles::BOLD, TextStyles::UNDERLINE, TextStyles::BLINK]);
        $this->assertEquals('', $styles->format(''));
    }

    /**
     * Tests formatting a string with all styles
     */
    public function testFormattingStringWithAllStyles()
    {
        $styles = new Style(Colors::RED, Colors::GREEN, [TextStyles::BOLD, TextStyles::UNDERLINE, TextStyles::BLINK]);
        $this->assertEquals("\033[31;42;1;4;5mfoo\033[39;49;22;24;25m", $styles->format('foo'));
    }

    /**
     * Tests formatting a string without styles
     */
    public function testFormattingStringWithoutStyles()
    {
        $styles = new Style();
        $this->assertEquals('foo', $styles->format('foo'));
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
        $style = new Style(Colors::BLUE, Colors::GREEN);
        $this->assertEquals(Colors::BLUE, $style->getForegroundColor());
        $this->assertEquals(Colors::GREEN, $style->getBackgroundColor());
    }

    /**
     * Tests removing an invalid text style
     */
    public function testRemovingInvalidTextStyle()
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->addTextStyle(TextStyles::BOLD);
        $style->removeTextStyle('foo');
    }

    /**
     * Tests removing a text style
     */
    public function testRemovingTextStyle()
    {
        $style = new Style(null, null, [TextStyles::BOLD]);
        $style->removeTextStyle(TextStyles::BOLD);
        $this->assertEquals([], $style->getTextStyles());
    }

    /**
     * Tests setting the background color
     */
    public function testSettingBackgroundColor()
    {
        $style = new Style();
        $style->setBackgroundColor(Colors::GREEN);
        $this->assertEquals(Colors::GREEN, $style->getBackgroundColor());
    }

    /**
     * Tests setting the foreground color
     */
    public function testSettingForegroundColor()
    {
        $style = new Style();
        $style->setForegroundColor(Colors::BLUE);
        $this->assertEquals(Colors::BLUE, $style->getForegroundColor());
    }

    /**
     * Tests setting the background color to an invalid value
     */
    public function testSettingInvalidBackgroundColor()
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->setBackgroundColor('foo');
    }

    /**
     * Tests setting the background color to an invalid value in the constructor
     */
    public function testSettingInvalidBackgroundColorInConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(null, 'foo');
    }

    /**
     * Tests setting the foreground color to an invalid value
     */
    public function testSettingInvalidForegroundColor()
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->setForegroundColor('foo');
    }

    /**
     * Tests setting the foreground color to an invalid value in the constructor
     */
    public function testSettingInvalidForegroundColorInConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Style('foo');
    }

    /**
     * Tests setting the text styles to an invalid value in the constructor
     */
    public function testSettingInvalidTextStylesInConstructor()
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(null, null, ['foo']);
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
