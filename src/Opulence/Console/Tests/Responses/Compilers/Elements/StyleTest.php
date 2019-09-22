<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    public function testAddingInvalidTextStyle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->addTextStyle('foo');
    }

    public function testAddingInvalidTextStyles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->addTextStyles(['foo']);
    }

    /**
     * Tests double-adding a text style
     */
    public function testDoubleAddingTextStyle(): void
    {
        $style = new Style();
        $style->addTextStyle(TextStyles::BOLD);
        $style->addTextStyle(TextStyles::BOLD);
        $style->addTextStyles([TextStyles::UNDERLINE, TextStyles::UNDERLINE]);
        $this->assertEquals([TextStyles::BOLD, TextStyles::UNDERLINE], $style->getTextStyles());
    }

    public function testFormattingEmptyString(): void
    {
        $styles = new Style(Colors::RED, Colors::GREEN, [TextStyles::BOLD, TextStyles::UNDERLINE, TextStyles::BLINK]);
        $this->assertEquals('', $styles->format(''));
    }

    public function testFormattingStringWithAllStyles(): void
    {
        $styles = new Style(Colors::RED, Colors::GREEN, [TextStyles::BOLD, TextStyles::UNDERLINE, TextStyles::BLINK]);
        $this->assertEquals("\033[31;42;1;4;5mfoo\033[39;49;22;24;25m", $styles->format('foo'));
    }

    public function testFormattingStringWithoutStyles(): void
    {
        $styles = new Style();
        $this->assertEquals('foo', $styles->format('foo'));
    }

    public function testNotPassingAnythingInConstructor(): void
    {
        $style = new Style();
        $this->assertNull($style->getForegroundColor());
        $this->assertNull($style->getBackgroundColor());
    }

    public function testPassingColorsInConstructor(): void
    {
        $style = new Style(Colors::BLUE, Colors::GREEN);
        $this->assertEquals(Colors::BLUE, $style->getForegroundColor());
        $this->assertEquals(Colors::GREEN, $style->getBackgroundColor());
    }

    public function testRemovingInvalidTextStyle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->addTextStyle(TextStyles::BOLD);
        $style->removeTextStyle('foo');
    }

    public function testRemovingTextStyle(): void
    {
        $style = new Style(null, null, [TextStyles::BOLD]);
        $style->removeTextStyle(TextStyles::BOLD);
        $this->assertEquals([], $style->getTextStyles());
    }

    public function testSettingBackgroundColor(): void
    {
        $style = new Style();
        $style->setBackgroundColor(Colors::GREEN);
        $this->assertEquals(Colors::GREEN, $style->getBackgroundColor());
    }

    public function testSettingForegroundColor(): void
    {
        $style = new Style();
        $style->setForegroundColor(Colors::BLUE);
        $this->assertEquals(Colors::BLUE, $style->getForegroundColor());
    }

    public function testSettingInvalidBackgroundColor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->setBackgroundColor('foo');
    }

    public function testSettingInvalidBackgroundColorInConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(null, 'foo');
    }

    public function testSettingInvalidForegroundColor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $style = new Style();
        $style->setForegroundColor('foo');
    }

    public function testSettingInvalidForegroundColorInConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Style('foo');
    }

    public function testSettingInvalidTextStylesInConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Style(null, null, ['foo']);
    }

    public function testSettingNullBackgroundColor(): void
    {
        $style = new Style();
        $style->setBackgroundColor(null);
        $this->assertNull($style->getBackgroundColor());
    }

    public function testSettingNullForegroundColor(): void
    {
        $style = new Style();
        $style->setForegroundColor(null);
        $this->assertNull($style->getForegroundColor());
    }
}
