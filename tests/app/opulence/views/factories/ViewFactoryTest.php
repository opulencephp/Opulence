<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the view factory
 */
namespace Opulence\Views\Factories;
use Opulence\Files\FileSystem;
use Opulence\Files\FileSystemException;
use Opulence\Tests\Views\Mocks\BarBuilder;
use Opulence\Tests\Views\Mocks\FooBuilder;
use Opulence\Views\IView;

class ViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileSystem The file system to use in tests */
    private $fileSystem = null;
    /** @var ViewFactory|\PHPUnit_Framework_MockObject_MockObject The view factory to use in tests */
    private $viewFactory = null;
    /** @var IView|\PHPUnit_Framework_MockObject_MockObject The view to use in tests */
    private $view = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new FileSystem();
        $this->viewFactory = $this->getMockForAbstractClass(ViewFactory::class, [$this->fileSystem, __DIR__ . "/../files"]);
        $this->viewFactory->expects($this->any())
            ->method("getExtension")
            ->willReturn("html");
        $this->view = $this->getMock(IView::class);
        $this->viewFactory->expects($this->any())
            ->method("createView")
            ->willReturn($this->view);
    }

    /**
     * Tests passing in a root directory with a trailing slash
     */
    public function testPassingInRootWithTrailingSlash()
    {
        $path = __DIR__ . "/../files/TestWithDefaultTagDelimiters.html";
        $expectedContent = $this->fileSystem->read($path);
        $this->viewFactory->expects($this->any())
            ->method("createView")
            ->with($path, $expectedContent);
        $this->viewFactory->create("TestWithDefaultTagDelimiters");
    }

    /**
     * Tests passing in a root directory without a trailing slash
     */
    public function testPassingInRootWithoutTrailingSlash()
    {
        $path = __DIR__ . "/../files/TestWithDefaultTagDelimiters.html";
        $expectedContent = $this->fileSystem->read($path);
        $this->viewFactory->expects($this->any())
            ->method("createView")
            ->with($path, $expectedContent);
        $this->viewFactory->create("TestWithDefaultTagDelimiters");
    }

    /**
     * Tests passing in a view path that does not exist
     */
    public function testPassingInViewPathThatDoesNotExist()
    {
        $this->setExpectedException(FileSystemException::class);
        $this->viewFactory->create("doesNotExist.html");
    }

    /**
     * Tests passing in a view path with a preceding slash
     */
    public function testPassingInViewPathWithPrecedingSlash()
    {
        $path = __DIR__ . "/../files/TestWithDefaultTagDelimiters.html";
        $expectedContent = $this->fileSystem->read($path);
        $this->viewFactory->expects($this->any())
            ->method("createView")
            ->with($path, $expectedContent);
        $this->viewFactory->create("/TestWithDefaultTagDelimiters");
    }

    /**
     * Tests passing in a view path without a preceding slash
     */
    public function testPassingInViewPathWithoutPrecedingSlash()
    {
        $path = __DIR__ . "/../files/TestWithDefaultTagDelimiters.html";
        $expectedContent = $this->fileSystem->read($path);
        $this->viewFactory->expects($this->any())
            ->method("createView")
            ->with($path, $expectedContent);
        $this->viewFactory->create("TestWithDefaultTagDelimiters");
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
        $this->view->expects($this->any())
            ->method("setVar")
            ->with("foo", "bar");
        $this->viewFactory->create("TestWithDefaultTagDelimiters");
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
        $this->view->expects($this->at(0))
            ->method("setVar")
            ->with("foo", "bar");
        $this->view->expects($this->at(1))
            ->method("setVar")
            ->with("foo", "bar");
        $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->viewFactory->create("TestWithCustomTagDelimiters");
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
        $this->view->expects($this->at(0))
            ->method("setVar")
            ->with("foo", "bar");
        $this->view->expects($this->at(1))
            ->method("setVar")
            ->with("bar", "baz");
        $this->viewFactory->create("TestWithDefaultTagDelimiters");
    }

    /**
     * Tests passing in a root directory without a trailing slash
     */
    public function testSettingRootWithoutTrailingSlash()
    {
        $this->viewFactory->setRootViewDirectory(__DIR__ . "/../files");
        $this->testPassingInRootWithoutTrailingSlash();
    }
}