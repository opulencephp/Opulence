<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Tests\Factories\IO;

use InvalidArgumentException;
use Opulence\Views\Factories\IO\FileViewReader;

/**
 * Tests the file view reader
 */
class FileViewReaderTest extends \PHPUnit\Framework\TestCase
{
    private FileViewReader $reader;

    protected function setUp(): void
    {
        $this->reader = new FileViewReader();
    }

    public function testExceptionThrownForInvalidPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reader->read(__DIR__ . '/fileThatDoesNotExist.html');
    }

    public function testReadingExistingFile(): void
    {
        $this->assertEquals('Foo', $this->reader->read(__DIR__ . '/../../files/Foo.html'));
    }
}
