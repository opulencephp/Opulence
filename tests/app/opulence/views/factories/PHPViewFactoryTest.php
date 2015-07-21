<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PHP view factory
 */
namespace Opulence\Views\Factories;
use Opulence\Files\FileSystem;
use Opulence\Views\View;

class PHPViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var PHPViewFactory The factory to use in tests */
    private $viewFactory = null;
    /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject The file system to use in tests */
    private $fileSystem = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = $this->getMock(FileSystem::class);
        $this->viewFactory = new PHPViewFactory($this->fileSystem, __DIR__);
    }

    /**
     * Tests that the correct view instance is created
     */
    public function testCorrectViewInstanceIsCreated()
    {
        $this->fileSystem->expects($this->any())
            ->method("read")
            ->with(__DIR__ . "/foo.php")
            ->willReturn("bar");
        $this->assertInstanceOf(View::class, $this->viewFactory->create("foo"));
    }

    /**
     * Tests that the extension is added correctly
     */
    public function testExtensionIsAddedCorrectly()
    {
        $this->fileSystem->expects($this->any())
            ->method("read")
            ->with(__DIR__ . "/foo.php")
            ->willReturn("bar");
        $this->viewFactory->create("foo");
    }
}