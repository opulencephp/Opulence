<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the file view reader
 */
namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

class FileViewReaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileViewReader The reader to use in tests */
    private $reader = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->reader = new FileViewReader();
    }

    /**
     * Tests exception is thrown for in valid path
     */
    public function testExceptionThrownForInvalidPath()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->reader->read(__DIR__ . "/fileThatDoesNotExist.html");
    }

    /**
     * Tests reading an existing file
     */
    public function testReadingExistingFile()
    {
        $this->assertEquals("Foo", $this->reader->read(__DIR__ . "/../../files/Foo.html"));
    }
}