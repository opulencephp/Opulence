<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Responses\Formatters;

use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\Formatters\TableFormatter;

/**
 * Tests the table formatter
 */
class TableFormatterTest extends \PHPUnit\Framework\TestCase
{
    /** @var TableFormatter The formatter to use in tests */
    private $formatter;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->formatter = new TableFormatter(new PaddingFormatter());
    }

    /**
     * Tests formatting an empty table
     */
    public function testFormattingEmptyTable(): void
    {
        $this->assertEmpty($this->formatter->format([]));
    }

    /**
     * Tests formatting a table with a single header and column
     */
    public function testFormattingSingleHeaderAndColumn(): void
    {
        $headers = ['foo'];
        $rows = [['a']];
        $expected =
            '+-----+' . PHP_EOL .
            '| foo |' . PHP_EOL .
            '+-----+' . PHP_EOL .
            '| a   |' . PHP_EOL .
            '+-----+';
        $this->assertEquals($expected, $this->formatter->format($rows, $headers));
    }

    /**
     * Tests formatting a table with a single row
     */
    public function testFormattingSingleRow(): void
    {
        $rows = [['a', 'bb', 'ccc']];
        $expected =
            '+---+----+-----+' . PHP_EOL .
            '| a | bb | ccc |' . PHP_EOL .
            '+---+----+-----+';
        $this->assertEquals($expected, $this->formatter->format($rows));
    }

    /**
     * Tests formatting a table with a single row and column
     */
    public function testFormattingSingleRowAndColumn(): void
    {
        $rows = [['a']];
        $expected =
            '+---+' . PHP_EOL .
            '| a |' . PHP_EOL .
            '+---+';
        $this->assertEquals($expected, $this->formatter->format($rows));
    }

    /**
     * Tests formatting a table with all custom characters
     */
    public function testFormattingTableWithCustomCharacters(): void
    {
        $headers = ['foo', 'bar'];
        $rows = [
            ['a'],
            ['aa', 'bb'],
            ['aaa', 'bbb', 'ccc']
        ];
        $this->formatter->setPadAfter(false);
        $this->formatter->setCellPaddingString('_');
        $this->formatter->setEolChar('<br>');
        $this->formatter->setVerticalBorderChar('I');
        $this->formatter->setHorizontalBorderChar('=');
        $this->formatter->setIntersectionChar('*');
        $expected =
            '*=====*=====*=====*<br>' .
            'I_foo_I_bar_I_   _I<br>' .
            '*=====*=====*=====*<br>' .
            'I_  a_I_   _I_   _I<br>' .
            'I_ aa_I_ bb_I_   _I<br>' .
            'I_aaa_I_bbb_I_ccc_I<br>' .
            '*=====*=====*=====*';
        $this->assertEquals($expected, $this->formatter->format($rows, $headers));
    }

    /**
     * Tests formatting a table with a custom padding string
     */
    public function testFormattingTableWithCustomPaddingString(): void
    {
        $rows = [['a']];
        $this->formatter->setCellPaddingString('__');
        $expected =
            '+-----+' . PHP_EOL .
            '|__a__|' . PHP_EOL .
            '+-----+';
        $this->assertEquals($expected, $this->formatter->format($rows));
    }

    /**
     * Tests formatting a table with headers but without rows
     */
    public function testFormattingTableWithHeadersButWithoutRows(): void
    {
        $this->assertEmpty($this->formatter->format([], ['foo', 'bar']));
    }

    /**
     * Tests formatting a table with more headers than row columns
     */
    public function testFormattingTableWithMoreHeadersThanRowColumns(): void
    {
        $headers = ['foo', 'bar', 'baz', 'blah'];
        $rows = [
            ['a'],
            ['aa', 'bb'],
            ['aaa', 'bbb', 'ccc']
        ];
        $expected =
            '+-----+-----+-----+------+' . PHP_EOL .
            '| foo | bar | baz | blah |' . PHP_EOL .
            '+-----+-----+-----+------+' . PHP_EOL .
            '| a   |     |     |      |' . PHP_EOL .
            '| aa  | bb  |     |      |' . PHP_EOL .
            '| aaa | bbb | ccc |      |' . PHP_EOL .
            '+-----+-----+-----+------+';
        $this->assertEquals($expected, $this->formatter->format($rows, $headers));
    }

    /**
     * Tests formatting a table with more row columns than headers
     */
    public function testFormattingTableWithMoreRowColumnsThanHeaders(): void
    {
        $headers = ['foo', 'bar'];
        $rows = [
            ['a'],
            ['aa', 'bb'],
            ['aaa', 'bbb', 'ccc']
        ];
        $expected =
            '+-----+-----+-----+' . PHP_EOL .
            '| foo | bar |     |' . PHP_EOL .
            '+-----+-----+-----+' . PHP_EOL .
            '| a   |     |     |' . PHP_EOL .
            '| aa  | bb  |     |' . PHP_EOL .
            '| aaa | bbb | ccc |' . PHP_EOL .
            '+-----+-----+-----+';
        $this->assertEquals($expected, $this->formatter->format($rows, $headers));
    }

    /**
     * Tests formatting a table without headers
     */
    public function testFormattingTableWithoutHeaders(): void
    {
        $rows = [
            ['a'],
            ['aa', 'bb'],
            ['aaa', 'bbb', 'ccc']
        ];
        $expected =
            '+-----+-----+-----+' . PHP_EOL .
            '| a   |     |     |' . PHP_EOL .
            '| aa  | bb  |     |' . PHP_EOL .
            '| aaa | bbb | ccc |' . PHP_EOL .
            '+-----+-----+-----+';
        $this->assertEquals($expected, $this->formatter->format($rows));
    }

    /**
     * Tests setting the rows to non-array values
     */
    public function testSettingRowsWithNonArrayValues(): void
    {
        $expected =
            '+-----+' . PHP_EOL .
            '| foo |' . PHP_EOL .
            '| bar |' . PHP_EOL .
            '+-----+';
        $this->assertEquals($expected, $this->formatter->format(['foo', 'bar']));
    }
}
