<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the table formatter
 */
namespace RDev\Console\Responses\Formatters;

class TableTest extends \PHPUnit_Framework_TestCase
{
    /** @var Table The formatter to use in tests */
    private $table = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->table = new Table(new Padding());
    }

    /**
     * Tests adding a header
     */
    public function testAddingHeader()
    {
        $headers = ["foo", "bar"];
        $this->table->setHeaders($headers);
        $this->table->addHeader("baz");
        $this->assertEquals(["foo", "bar", "baz"], $this->table->getHeaders());
    }

    /**
     * Tests adding a row
     */
    public function testAddingRow()
    {
        $rows = [["foo"], ["bar"]];
        $this->table->setRows($rows);
        $this->table->addRow(["baz"]);
        $this->assertEquals([["foo"], ["bar"], ["baz"]], $this->table->getRows());
    }

    /**
     * Tests formatting an empty table
     */
    public function testFormattingEmptyTable()
    {
        $this->assertEmpty($this->table->format());
    }

    /**
     * Tests formatting a table with a single header and column
     */
    public function testFormattingSingleHeaderAndColumn()
    {
        $headers = ["foo"];
        $rows = [["a"]];
        $this->table->setHeaders($headers);
        $this->table->setRows($rows);
        $expected =
            "+-----+" . PHP_EOL .
            "| foo |" . PHP_EOL .
            "+-----+" . PHP_EOL .
            "| a   |" . PHP_EOL .
            "+-----+";
        $this->assertEquals($expected, $this->table->format());
    }

    /**
     * Tests formatting a table with a single row
     */
    public function testFormattingSingleRow()
    {
        $rows = [["a", "bb", "ccc"]];
        $this->table->setRows($rows);
        $expected =
            "+---+----+-----+" . PHP_EOL .
            "| a | bb | ccc |" . PHP_EOL .
            "+---+----+-----+";
        $this->assertEquals($expected, $this->table->format());
    }

    /**
     * Tests formatting a table with a single row and column
     */
    public function testFormattingSingleRowAndColumn()
    {
        $rows = [["a"]];
        $this->table->setRows($rows);
        $expected =
            "+---+" . PHP_EOL .
            "| a |" . PHP_EOL .
            "+---+";
        $this->assertEquals($expected, $this->table->format());
    }

    /**
     * Tests formatting a table with all custom characters
     */
    public function testFormattingTableWithCustomCharacters()
    {
        $headers = ["foo", "bar"];
        $rows = [
            ["a"],
            ["aa", "bb"],
            ["aaa", "bbb", "ccc"]
        ];
        $this->table->setHeaders($headers);
        $this->table->setRows($rows);
        $expected =
            "*=====*=====*=====*<br>".
            "I_foo_I_bar_I_   _I<br>" .
            "*=====*=====*=====*<br>" .
            "I_  a_I_   _I_   _I<br>" .
            "I_ aa_I_ bb_I_   _I<br>" .
            "I_aaa_I_bbb_I_ccc_I<br>" .
            "*=====*=====*=====*";
        $this->assertEquals($expected, $this->table->format(false, "_", "<br>", "I", "=", "*"));
    }

    /**
     * Tests formatting a table with headers but without rows
     */
    public function testFormattingTableWithHeadersButWithoutRows()
    {
        $this->table->setHeaders(["foo", "bar"]);
        $this->assertEmpty($this->table->format());
    }

    /**
     * Tests formatting a table with more headers than row columns
     */
    public function testFormattingTableWithMoreHeadersThanRowColumns()
    {
        $headers = ["foo", "bar", "baz", "blah"];
        $rows = [
            ["a"],
            ["aa", "bb"],
            ["aaa", "bbb", "ccc"]
        ];
        $this->table->setHeaders($headers);
        $this->table->setRows($rows);
        $expected =
            "+-----+-----+-----+------+" . PHP_EOL .
            "| foo | bar | baz | blah |" . PHP_EOL .
            "+-----+-----+-----+------+" . PHP_EOL .
            "| a   |     |     |      |" . PHP_EOL .
            "| aa  | bb  |     |      |" . PHP_EOL .
            "| aaa | bbb | ccc |      |" . PHP_EOL .
            "+-----+-----+-----+------+";
        $this->assertEquals($expected, $this->table->format());
    }

    /**
     * Tests formatting a table with more row columns than headers
     */
    public function testFormattingTableWithMoreRowColumnsThanHeaders()
    {
        $headers = ["foo", "bar"];
        $rows = [
            ["a"],
            ["aa", "bb"],
            ["aaa", "bbb", "ccc"]
        ];
        $this->table->setHeaders($headers);
        $this->table->setRows($rows);
        $expected =
            "+-----+-----+-----+" . PHP_EOL .
            "| foo | bar |     |" . PHP_EOL .
            "+-----+-----+-----+" . PHP_EOL .
            "| a   |     |     |" . PHP_EOL .
            "| aa  | bb  |     |" . PHP_EOL .
            "| aaa | bbb | ccc |" . PHP_EOL .
            "+-----+-----+-----+";
        $this->assertEquals($expected, $this->table->format());
    }

    /**
     * Tests formatting a table without headers
     */
    public function testFormattingTableWithoutHeaders()
    {
        $rows = [
            ["a"],
            ["aa", "bb"],
            ["aaa", "bbb", "ccc"]
        ];
        $this->table->setRows($rows);
        $expected =
            "+-----+-----+-----+" . PHP_EOL .
            "| a   |     |     |" . PHP_EOL .
            "| aa  | bb  |     |" . PHP_EOL .
            "| aaa | bbb | ccc |" . PHP_EOL .
            "+-----+-----+-----+";
        $this->assertEquals($expected, $this->table->format());
    }

    /**
     * Tests getting the rows
     */
    public function testGettingRows()
    {
        $this->assertEquals([], $this->table->getRows());
    }

    /**
     * Tests setting the headers
     */
    public function testSettingHeaders()
    {
        $headers = ["foo", "bar"];
        $this->table->setHeaders($headers);
        $this->assertEquals($headers, $this->table->getHeaders());
    }

    /**
     * Tests setting the rows
     */
    public function testSettingRows()
    {
        $rows = [["foo"], ["bar"]];
        $this->table->setRows($rows);
        $this->assertEquals($rows, $this->table->getRows());
    }

    /**
     * Tests setting the rows to non-array values
     */
    public function testSettingRowsWithNonArrayValues()
    {
        $this->table->setRows(["foo", "bar"]);
        $this->assertEquals([["foo"], ["bar"]], $this->table->getRows());
    }
}