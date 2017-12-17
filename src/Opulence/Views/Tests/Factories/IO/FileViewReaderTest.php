<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Views\Tests\Factories\IO;

use InvalidArgumentException;
use Opulence\Views\Factories\IO\FileViewReader;

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
    public function setUp() : void
    {
        $this->reader = new FileViewReader();
    }

    /**
     * Tests exception is thrown for in valid path
     */
    public function testExceptionThrownForInvalidPath() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reader->read(__DIR__ . '/fileThatDoesNotExist.html');
    }

    /**
     * Tests reading an existing file
     */
    public function testReadingExistingFile() : void
    {
        $this->assertEquals('Foo', $this->reader->read(__DIR__ . '/../../files/Foo.html'));
    }
}
