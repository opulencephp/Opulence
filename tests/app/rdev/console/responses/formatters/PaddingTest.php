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
            ["a", "b"],
            ["cd", "e"],
            [" fg ", "h"],
            ["ijk", "l"]
        ];
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        }, true, " ", "<br>");
        $this->assertEquals("a  -b<br>cd -e<br>fg -h<br>ijk-l", $formattedText);
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
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line;
        }, true, " ", "<br>");
        $this->assertEquals("a  <br>cd <br>fg <br>ijk", $formattedText);
    }

    /**
     * Tests a custom padding string with array lines
     */
    public function testCustomPaddingStringWithArrayLines()
    {
        $lines = [
            ["a", "b"],
            ["cd", "e"],
            [" fg ", "h"],
            ["ijk", "l"]
        ];
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        }, true, "+", PHP_EOL);
        $this->assertEquals("a++-b" . PHP_EOL . "cd+-e" . PHP_EOL . "fg+-h" . PHP_EOL . "ijk-l", $formattedText);
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
        $formattedText = $this->formatter->format($lines, function($line)
        {
            return $line;
        }, true, "+", PHP_EOL);
        $this->assertEquals("a++" . PHP_EOL . "cd+" . PHP_EOL . "fg+" . PHP_EOL . "ijk", $formattedText);
    }

    /**
     * Tests padding array lines
     */
    public function testPaddingArrayLines()
    {
        $lines = [
            ["a", "b"],
            ["cd", "e"],
            [" fg ", "h"],
            ["ijk", "l"]
        ];
        // Format with the padding after the string
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        }, true);
        $this->assertEquals("a  -b" . PHP_EOL . "cd -e" . PHP_EOL . "fg -h" . PHP_EOL . "ijk-l", $formattedLines);
        // Format with the padding before the string
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line[0] . "-" . $line[1];
        }, false);
        $this->assertEquals("  a-b" . PHP_EOL . " cd-e" . PHP_EOL . " fg-h" . PHP_EOL . "ijk-l", $formattedLines);
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
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line;
        }, true);
        $this->assertEquals("a  " . PHP_EOL . "cd " . PHP_EOL . "fg " . PHP_EOL . "ijk", $formattedLines);
        // Format with the padding before the string
        $formattedLines = $this->formatter->format($lines, function($line)
        {
            return $line;
        }, false);
        $this->assertEquals("  a" . PHP_EOL . " cd" . PHP_EOL . " fg" . PHP_EOL . "ijk", $formattedLines);
    }

    /**
     * Tests passing lines with incorrectly set items
     */
    public function testPassingLinesWithIncorrectlySetItems()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->formatter->format([["foo"]], function($line)
        {
            return $line;
        });
    }
}