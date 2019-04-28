<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Tests\Composer;

use Opulence\Framework\Tests\Composer\Mocks\Executable;

/**
 * Tests the Composer executable wrapper
 */
class ExecutableTest extends \PHPUnit\Framework\TestCase
{
    /** @var Executable The executable without a composer.phar to use in tests */
    private $executableWithoutPHAR;
    /** @var Executable The executable with a composer.phar to use in tests */
    private $executableWithPHAR;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->executableWithoutPHAR = new Executable(__DIR__);
        $this->executableWithPHAR = new Executable(__DIR__ . '/Mocks');
    }

    /**
     * Tests the dump autoload command
     */
    public function testDumpAutoload(): void
    {
        $this->assertEquals('composer dump-autoload ', $this->executableWithoutPHAR->dumpAutoload());
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar dump-autoload ',
            $this->executableWithPHAR->dumpAutoload()
        );
    }

    /**
     * Tests the dump autoload command with options
     */
    public function testDumpAutoloadWithOptions(): void
    {
        $this->assertEquals('composer dump-autoload -o', $this->executableWithoutPHAR->dumpAutoload('-o'));
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar dump-autoload -o',
            $this->executableWithPHAR->dumpAutoload('-o')
        );
    }

    /**
     * Tests the update command
     */
    public function testUpdate(): void
    {
        $this->assertEquals('composer update ', $this->executableWithoutPHAR->update());
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar update ',
            $this->executableWithPHAR->update()
        );
    }

    /**
     * Tests the update command with options
     */
    public function testUpdateWithOptions(): void
    {
        $this->assertEquals('composer update --prefer-dist', $this->executableWithoutPHAR->update('--prefer-dist'));
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar update --prefer-dist',
            $this->executableWithPHAR->update('--prefer-dist')
        );
    }
}
