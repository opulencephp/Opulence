<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

/**
 * Tests the file view reader
 */
class FileViewReaderTest extends \PHPUnit\Framework\TestCase
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
        $this->expectException(InvalidArgumentException::class);
        $this->reader->read(__DIR__ . '/fileThatDoesNotExist.html');
    }

    /**
     * Tests reading an existing file
     */
    public function testReadingExistingFile()
    {
        $this->assertEquals('Foo', $this->reader->read(__DIR__ . '/../../files/Foo.html'));
    }
}
