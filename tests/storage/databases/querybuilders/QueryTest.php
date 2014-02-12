<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Tests our query class
 */
namespace RamODev\Storage\Databases\QueryBuilders;

require_once(__DIR__ . "/../../../../storage/databases/querybuilders/Query.php");

class QueryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Query The query object stub */
    private $query = null;

    /**
     * Sets up our test
     */
    public function setUp()
    {
        $this->query = $this->getMockForAbstractClass("\\RamODev\\Storage\\Databases\\QueryBuilders\\Query");
    }

    /**
     * Tests adding a named placeholder
     */
    public function testAddingNamedPlaceholder()
    {
        $this->query->addNamedPlaceholderValue("userID", 18175);
        $this->assertEquals(array("userID" => 18175), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when we add a named placeholder after an unnamed one
     */
    public function testAddingNamedPlaceholderAfterAddingUnnamedPlaceholder()
    {
        $this->setExpectedException("RamODev\\Storage\\Databases\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addUnnamedPlaceholderValue("dave")
            ->addNamedPlaceholderValue("id", 18175);
    }

    /**
     * Tests adding an unnamed placeholder
     */
    public function testAddingUnnamedPlaceholder()
    {
        $this->query->addUnnamedPlaceholderValue(18175);
        $this->assertEquals(array(18175), $this->query->getParameters());
    }

    /**
     * Tests the exception that should be thrown when we add an unnamed placeholder after a named one
     */
    public function testAddingUnnamedPlaceholderAfterAddingNamedPlaceholder()
    {
        $this->setExpectedException("RamODev\\Storage\\Databases\\QueryBuilders\\Exceptions\\InvalidQueryException");
        $this->query->addNamedPlaceholderValue("id", 18175)
            ->addUnnamedPlaceholderValue("dave");
    }
} 