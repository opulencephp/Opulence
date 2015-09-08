<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view factory
 */
namespace Opulence\Views\Factories;
use Opulence\Files\FileSystem;
use Opulence\Tests\Views\Factories\Mocks\BarBuilder;
use Opulence\Tests\Views\Factories\Mocks\FooBuilder;

class ViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var IViewNameResolver|\PHPUnit_Framework_MockObject_MockObject The view name resolver to use in tests */
    private $viewNameResolver = null;
    /** @var FileSystem|\PHPUnit_Framework_MockObject_MockObject The file system to use in tests */
    private $fileSystem = null;
    /** @var ViewFactory The view factory to use in tests */
    private $viewFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->viewNameResolver = $this->getMock(IViewNameResolver::class);
        $this->fileSystem = $this->getMock(FileSystem::class);
        $this->fileSystem->expects($this->any())
            ->method("read")
            ->willReturn("foo");
        $this->viewFactory = $this->getMock(
            ViewFactory::class, null,
            [$this->viewNameResolver, $this->fileSystem]
        );
    }

    /**
     * Tests registering a builder
     */
    public function testRegisteringBuilder()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function ()
        {
            return new FooBuilder();
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
    }

    /**
     * Tests registering builders to multiple paths
     */
    public function testRegisteringBuilderToMultiplePaths()
    {
        $this->viewFactory->registerBuilder(["TestWithDefaultTagDelimiters", "TestWithCustomTagDelimiters"], function ()
        {
            return new FooBuilder();
        });
        $this->viewNameResolver->expects($this->at(0))
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->viewNameResolver->expects($this->at(1))
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithCustomTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
        $view = $this->viewFactory->create("TestWithCustomTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
    }

    /**
     * Tests registering multiple builders
     */
    public function testRegisteringMultipleBuilders()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function ()
        {
            return new FooBuilder();
        });
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function ()
        {
            return new BarBuilder();
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
        $this->assertEquals("baz", $view->getVar("bar"));
    }
}