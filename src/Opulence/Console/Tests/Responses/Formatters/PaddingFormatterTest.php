<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Formatters;

use Opulence\Console\Responses\Formatters\PaddingFormatter;

/**
 * Tests the padding formatter
 */
class PaddingFormatterTest extends \PHPUnit\Framework\TestCase
{
    /** @var PaddingFormatter The formatter to use in tests */
    private $formatter = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->formatter = new PaddingFormatter();
    }

    /**
     * Tests a custom padding string with array rows
     */
    public function testCustomPaddingStringWithArrayRows()
    {
        $rows = [
            ['a', 'b '],
            ['cd', ' ee'],
            [' fg ', 'hhh'],
            ['ijk', 'll ']
        ];
        $this->formatter->setPaddingString('+');
        $formattedText = $this->formatter->format($rows, function ($row) {
            return $row[0] . '-' . $row[1];
        });
        $this->assertEquals('a++-b++' . PHP_EOL . 'cd+-ee+' . PHP_EOL . 'fg+-hhh' . PHP_EOL . 'ijk-ll+',
            $formattedText);
    }

    /**
     * Tests a custom padding string with string rows
     */
    public function testCustomPaddingStringWithStringRows()
    {
        $rows = [
            'a',
            'cd',
            ' fg ',
            'ijk'
        ];
        $this->formatter->setPaddingString('+');
        $formattedText = $this->formatter->format($rows, function ($row) {
            return $row[0];
        });
        $this->assertEquals('a++' . PHP_EOL . 'cd+' . PHP_EOL . 'fg+' . PHP_EOL . 'ijk', $formattedText);
    }

    /**
     * Tests a custom row separator with row arrays
     */
    public function testCustomRowSeparatorWithRowArrays()
    {
        $rows = [
            ['a', '  b'],
            ['cd', ' ee'],
            [' fg ', 'hhh'],
            ['ijk', ' ll']
        ];
        $this->formatter->setEolChar('<br>');
        $formattedText = $this->formatter->format($rows, function ($row) {
            return $row[0] . '-' . $row[1];
        });
        $this->assertEquals('a  -b  <br>cd -ee <br>fg -hhh<br>ijk-ll ', $formattedText);
    }

    /**
     * Tests a custom row separator with string rows
     */
    public function testCustomRowSeparatorWithStringRows()
    {
        $rows = [
            'a',
            'cd',
            ' fg ',
            'ijk'
        ];
        $this->formatter->setEolChar('<br>');
        $formattedText = $this->formatter->format($rows, function ($row) {
            return $row[0];
        });
        $this->assertEquals('a  <br>cd <br>fg <br>ijk', $formattedText);
    }

    /**
     * Tests getting the EOL char
     */
    public function testGettingEOLChar()
    {
        $this->formatter->setEolChar('foo');
        $this->assertEquals('foo', $this->formatter->getEolChar());
    }

    /**
     * Tests normalizing the columns
     */
    public function testNormalizingColumns()
    {
        $rows = [
            ['a'],
            ['aa', 'bbbb'],
            ['aaa', 'bbb', 'ccc'],
            ['aaa', 'bbb', 'ccc', 'ddddd']
        ];
        $expected = [
            ['a', '', '', ''],
            ['aa', 'bbbb', '', ''],
            ['aaa', 'bbb', 'ccc', ''],
            ['aaa', 'bbb', 'ccc', 'ddddd']
        ];
        $this->assertEquals([3, 4, 3, 5], $this->formatter->normalizeColumns($rows));
        $this->assertEquals($expected, $rows);
    }

    /**
     * Tests padding array rows
     */
    public function testPaddingArrayRows()
    {
        $rows = [
            ['a', 'b'],
            ['cd', 'ee '],
            [' fg ', 'hhh'],
            ['ijk', ' ll']
        ];
        // Format with the padding after the string
        $this->formatter->setPadAfter(true);
        $formattedRows = $this->formatter->format($rows, function ($row) {
            return $row[0] . '-' . $row[1];
        });
        $this->assertEquals('a  -b  ' . PHP_EOL . 'cd -ee ' . PHP_EOL . 'fg -hhh' . PHP_EOL . 'ijk-ll ',
            $formattedRows);
        // Format with the padding before the string
        $this->formatter->setPadAfter(false);
        $formattedRows = $this->formatter->format($rows, function ($row) {
            return $row[0] . '-' . $row[1];
        });
        $this->assertEquals('  a-  b' . PHP_EOL . ' cd- ee' . PHP_EOL . ' fg-hhh' . PHP_EOL . 'ijk- ll',
            $formattedRows);
    }

    /**
     * Tests padding empty array
     */
    public function testPaddingEmptyArray()
    {
        $this->assertEquals('', $this->formatter->format([], function ($row) {
            return $row[0];
        }));
    }

    /**
     * Tests padding a single array
     */
    public function testPaddingSingleArray()
    {
        $this->assertEquals('foo' . PHP_EOL . 'bar', $this->formatter->format(['  foo  ', 'bar'], function ($row) {
            return $row[0];
        }));
    }

    /**
     * Tests padding a single string
     */
    public function testPaddingSingleString()
    {
        $this->assertEquals('foo', $this->formatter->format(['  foo  '], function ($row) {
            return $row[0];
        }));
    }

    /**
     * Tests padding string rows
     */
    public function testPaddingStringRows()
    {
        $rows = [
            'a',
            'cd',
            ' fg ',
            'ijk'
        ];
        // Format with the padding after the string
        $this->formatter->setPadAfter(true);
        $formattedRows = $this->formatter->format($rows, function ($row) {
            return $row[0];
        });
        $this->assertEquals('a  ' . PHP_EOL . 'cd ' . PHP_EOL . 'fg ' . PHP_EOL . 'ijk', $formattedRows);
        // Format with the padding before the string
        $this->formatter->setPadAfter(false);
        $formattedRows = $this->formatter->format($rows, function ($row) {
            return $row[0];
        });
        $this->assertEquals('  a' . PHP_EOL . ' cd' . PHP_EOL . ' fg' . PHP_EOL . 'ijk', $formattedRows);
    }
}
