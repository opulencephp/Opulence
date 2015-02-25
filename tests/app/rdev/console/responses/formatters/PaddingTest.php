<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the padding formatter
 */
namespace RDev\Console\Responses\Formatters;

class PaddingTest extends \PHPUnit_Framework_TestCase
{
    /** @var Padding The formatter to use in tests */
    private $formatter = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->formatter = new Padding();
    }

    /**
     * Tests a custom line separator with array lines
     */
    public function testCustomLineSeparatorWithArrayLines()
    {
        $lines = [
            ["a", "  b"],
            ["cd", " ee"],
            [" fg ", "hhh"],
            ["ijk", " ll"]
        ];
        $this->formatter->setEOLChar("<br>");
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        });
        $this->assertEquals("a  -b  <br>cd -ee <br>fg -hhh<br>ijk-ll ", $formattedText);
    }

    /**
     * Tests a custom line separator with string lines
     */
    public function testCustomLineSeparatorWithStringLines()
    {
        $lines = [
            "a",
            "cd",
            " fg ",
            "ijk"
        ];
        $this->formatter->setEOLChar("<br>");
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line;
        });
        $this->assertEquals("a  <br>cd <br>fg <br>ijk", $formattedText);
    }

    /**
     * Tests a custom padding string with array lines
     */
    public function testCustomPaddingStringWithArrayLines()
    {
        $lines = [
            ["a", "b "],
            ["cd", " ee"],
            [" fg ", "hhh"],
            ["ijk", "ll "]
        ];
        $this->formatter->setPaddingString("+");
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        });
        $this->assertEquals("a++-b++" . PHP_EOL . "cd+-ee+" . PHP_EOL . "fg+-hhh" . PHP_EOL . "ijk-ll+", $formattedText);
    }

    /**
     * Tests a custom padding string with string lines
     */
    public function testCustomPaddingStringWithStringLines()
    {
        $lines = [
            "a",
            "cd",
            " fg ",
            "ijk"
        ];
        $this->formatter->setPaddingString("+");
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line;
        });
        $this->assertEquals("a++" . PHP_EOL . "cd+" . PHP_EOL . "fg+" . PHP_EOL . "ijk", $formattedText);
    }

    /**
     * Tests equalizing line lengths
     */
    public function testEqualizingLineLengths()
    {
        $lines = [
            ["foo"],
            ["foo", "bar"],
            ["foo", "bar", "baz"]
        ];
        $this->assertEquals(3, $this->formatter->equalizeLineLengths($lines));
        $this->assertEquals([
            ["foo", "", ""],
            ["foo", "bar", ""],
            ["foo", "bar", "baz"]
        ], $lines);
    }

    /**
     * Tests getting the EOL char
     */
    public function testGettingEOLChar()
    {
        $this->formatter->setEOLChar("foo");
        $this->assertEquals("foo", $this->formatter->getEOLChar());
    }

    /**
     * Tests getting the max lengths
     */
    public function testGettingMaxLengths()
    {
        $lines = [
            ["a"],
            ["aa", "bbbb"],
            ["aaa", "bbb", "ccc"],
            ["aaa", "bbb", "ccc", "ddddd"]
        ];
        $this->assertEquals([3, 4, 3, 5], $this->formatter->getMaxLengths($lines));
    }

    /**
     * Tests padding array lines
     */
    public function testPaddingArrayLines()
    {
        $lines = [
            ["a", "b"],
            ["cd", "ee "],
            [" fg ", "hhh"],
            ["ijk", " ll"]
        ];
        // Format with the padding after the string
        $this->formatter->setPadAfter(true);
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        });
        $this->assertEquals("a  -b  " . PHP_EOL . "cd -ee " . PHP_EOL . "fg -hhh" . PHP_EOL . "ijk-ll ", $formattedLines);
        // Format with the padding before the string
        $this->formatter->setPadAfter(false);
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        });
        $this->assertEquals("  a-  b" . PHP_EOL . " cd- ee" . PHP_EOL . " fg-hhh" . PHP_EOL . "ijk- ll", $formattedLines);
    }

    /**
     * Tests padding empty array
     */
    public function testPaddingEmptyArray()
    {
        $this->assertEquals("", $this->formatter->format([], function($line)
        {
            return $line;
        }));
    }

    /**
     * Tests padding a single array
     */
    public function testPaddingSingleArray()
    {
        $this->assertEquals("foo" . PHP_EOL . "bar", $this->formatter->format(["  foo  ", "bar"], function($line)
        {
            return $line;
        }));
    }

    /**
     * Tests padding a single string
     */
    public function testPaddingSingleString()
    {
        $this->assertEquals("foo", $this->formatter->format(["  foo  "], function($line)
        {
            return $line;
        }));
    }

    /**
     * Tests padding string lines
     */
    public function testPaddingStringLines()
    {
        $lines = [
            "a",
            "cd",
            " fg ",
            "ijk"
        ];
        // Format with the padding after the string
        $this->formatter->setPadAfter(true);
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line;
        });
        $this->assertEquals("a  " . PHP_EOL . "cd " . PHP_EOL . "fg " . PHP_EOL . "ijk", $formattedLines);
        // Format with the padding before the string
        $this->formatter->setPadAfter(false);
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line;
        });
        $this->assertEquals("  a" . PHP_EOL . " cd" . PHP_EOL . " fg" . PHP_EOL . "ijk", $formattedLines);
    }
}