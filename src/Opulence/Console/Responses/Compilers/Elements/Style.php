<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Responses\Compilers\Elements;

use InvalidArgumentException;

/**
 * Defines the style of an element
 */
class Style
{
    /**
     * The list of possible foreground colors
     *
     * @link http://en.wikipedia.org/wiki/ANSI_escape_code
     * @var array
     */
    private static $supportedForegroundColors = [
        Colors::BLACK => [30, 39],
        Colors::RED => [31, 39],
        Colors::GREEN => [32, 39],
        Colors::YELLOW => [33, 39],
        Colors::BLUE => [34, 39],
        Colors::MAGENTA => [35, 39],
        Colors::CYAN => [36, 39],
        Colors::WHITE => [37, 39]
    ];
    /**
     * The list of possible background colors
     *
     * @link http://en.wikipedia.org/wiki/ANSI_escape_code
     * @var array
     */
    private static $supportedBackgroundColors = [
        Colors::BLACK => [40, 49],
        Colors::RED => [41, 49],
        Colors::GREEN => [42, 49],
        Colors::YELLOW => [43, 49],
        Colors::BLUE => [44, 49],
        Colors::MAGENTA => [45, 49],
        Colors::CYAN => [46, 49],
        Colors::WHITE => [47, 49]
    ];
    /**
     * The list of possible text styles
     *
     * @link http://en.wikipedia.org/wiki/ANSI_escape_code
     * @var array
     */
    private static $supportedTextStyles = [
        TextStyles::BOLD => [1, 22],
        TextStyles::UNDERLINE => [4, 24],
        TextStyles::BLINK => [5, 25]
    ];
    /** @var string|null The foreground color */
    private $foregroundColor = null;
    /** @var string|null The background color */
    private $backgroundColor = null;
    /** @var array The list of text styles */
    private $textStyles = [];

    /**
     * @param string|null $foregroundColor The foreground color
     * @param string|null $backgroundColor The background color
     * @param array $textStyles The list of text styles to apply
     */
    public function __construct(string $foregroundColor = null, string $backgroundColor = null, array $textStyles = [])
    {
        $this->setForegroundColor($foregroundColor);
        $this->setBackgroundColor($backgroundColor);
        $this->addTextStyles($textStyles);
    }

    /**
     * Adds the text to have a certain style
     *
     * @param string $style The name of the text style
     * @throws InvalidArgumentException Thrown if the text style does not exist
     */
    public function addTextStyle(string $style)
    {
        if (!isset(self::$supportedTextStyles[$style])) {
            throw new InvalidArgumentException("Invalid text style \"$style\"");
        }

        // Don't double-add a style
        if (!in_array($style, $this->textStyles)) {
            $this->textStyles[] = $style;
        }
    }

    /**
     * Adds multiple text styles
     *
     * @param array $styles The names of the text styles
     * @throws InvalidArgumentException Thrown if the text styles do not exist
     */
    public function addTextStyles(array $styles)
    {
        foreach ($styles as $style) {
            $this->addTextStyle($style);
        }
    }

    /**
     * Formats text with the the currently-set styles
     *
     * @param string $text The text to format
     * @return string The formatted text
     */
    public function format(string $text) : string
    {
        if ($text === "") {
            return $text;
        }

        $startCodes = [];
        $endCodes = [];

        if ($this->foregroundColor !== null) {
            $startCodes[] = self::$supportedForegroundColors[$this->foregroundColor][0];
            $endCodes[] = self::$supportedForegroundColors[$this->foregroundColor][1];
        }

        if ($this->backgroundColor !== null) {
            $startCodes[] = self::$supportedBackgroundColors[$this->backgroundColor][0];
            $endCodes[] = self::$supportedBackgroundColors[$this->backgroundColor][1];
        }

        foreach ($this->textStyles as $style) {
            $startCodes[] = self::$supportedTextStyles[$style][0];
            $endCodes[] = self::$supportedTextStyles[$style][1];
        }

        if (count($startCodes) == 0 && count($endCodes) == 0) {
            // No point in trying to format the text
            return $text;
        }

        return sprintf(
            "\033[%sm%s\033[%sm",
            implode(";", $startCodes),
            $text,
            implode(";", $endCodes)
        );
    }

    /**
     * @return string|null
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @return string|null
     */
    public function getForegroundColor()
    {
        return $this->foregroundColor;
    }

    /**
     * @return array
     */
    public function getTextStyles() : array
    {
        return $this->textStyles;
    }

    /**
     * Removes a text style
     *
     * @param string $style The style to remove
     * @throws InvalidArgumentException Thrown if the text style is invalid
     */
    public function removeTextStyle(string $style)
    {
        if (!isset(self::$supportedTextStyles[$style])) {
            throw new InvalidArgumentException("Invalid text style \"$style\"");
        }

        if (($index = array_search($style, $this->textStyles)) !== false) {
            unset($this->textStyles[$index]);
        }
    }

    /**
     * @param string|null $backgroundColor
     * @throws InvalidArgumentException Thrown if the color was invalid
     */
    public function setBackgroundColor(string $backgroundColor = null)
    {
        if ($backgroundColor !== null && !isset(self::$supportedBackgroundColors[$backgroundColor])) {
            throw new InvalidArgumentException("Invalid background color \"$backgroundColor\"");
        }

        $this->backgroundColor = $backgroundColor;
    }

    /**
     * @param string|null $foregroundColor
     * @throws InvalidArgumentException Thrown if the color was invalid
     */
    public function setForegroundColor(string $foregroundColor = null)
    {
        if ($foregroundColor !== null && !isset(self::$supportedForegroundColors[$foregroundColor])) {
            throw new InvalidArgumentException("Invalid foreground color \"$foregroundColor\"");
        }

        $this->foregroundColor = $foregroundColor;
    }
}