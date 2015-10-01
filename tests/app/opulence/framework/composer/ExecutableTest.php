<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Composer executable wrapper
 */
namespace Opulence\Framework\Composer;

use Opulence\Applications\Paths;
use Opulence\Tests\Framework\Composer\Mocks\Executable;

class ExecutableTest extends \PHPUnit_Framework_TestCase
{
    /** @var Executable The executable without a composer.phar to use in tests */
    private $executableWithoutPHAR = null;
    /** @var Executable The executable with a composer.phar to use in tests */
    private $executableWithPHAR = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $paths = new Paths(["root" => __DIR__]);
        $this->executableWithoutPHAR = new Executable($paths);
        $paths["root"] = __DIR__ . "/mocks";
        $this->executableWithPHAR = new Executable($paths);
    }

    /**
     * Tests the dump autoload command
     */
    public function testDumpAutoload()
    {
        $this->assertEquals("composer dump-autoload ", $this->executableWithoutPHAR->dumpAutoload());
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar dump-autoload ',
            $this->executableWithPHAR->dumpAutoload()
        );
    }

    /**
     * Tests the dump autoload command with options
     */
    public function testDumpAutoloadWithOptions()
    {
        $this->assertEquals("composer dump-autoload -o", $this->executableWithoutPHAR->dumpAutoload("-o"));
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar dump-autoload -o',
            $this->executableWithPHAR->dumpAutoload("-o")
        );
    }

    /**
     * Tests the update command
     */
    public function testUpdate()
    {
        $this->assertEquals("composer update ", $this->executableWithoutPHAR->update());
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar update ',
            $this->executableWithPHAR->update()
        );
    }

    /**
     * Tests the update command with options
     */
    public function testUpdateWithOptions()
    {
        $this->assertEquals("composer update --prefer-dist", $this->executableWithoutPHAR->update("--prefer-dist"));
        $this->assertEquals(
            '"' . PHP_BINARY . '" composer.phar update --prefer-dist',
            $this->executableWithPHAR->update("--prefer-dist")
        );
    }
}