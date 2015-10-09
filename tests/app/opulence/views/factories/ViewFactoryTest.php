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
use Opulence\Views\IView;

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
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function (IView $view) {
            return (new FooBuilder())->build($view);
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
        $this->viewFactory->registerBuilder(["TestWithDefaultTagDelimiters", "TestWithCustomTagDelimiters"],
            function (IView $view) {
                return (new FooBuilder())->build($view);
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
     * Tests registering a builder for a view name and then creating that view with the exact same view name
     */
    public function testRegisteringBuilderWithExactSameNameAsView()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.html", function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $this->assertEquals("bar", $view->getVar("foo"));
    }

    /**
     * Tests registering a builder for a view with an extension and then creating that view without an extension
     */
    public function testRegisteringBuilderWithExtensionAndCreatingSameViewWithoutExtension()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.html", function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
    }

    /**
     * Tests registering a builder with the same basename as a view, but resolves to a different view file
     */
    public function testRegisteringBuilderWithSameBasenameAsViewButResolvesToDifferentViewFile()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.foo.php", function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->at(0))
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->viewNameResolver->expects($this->at(0))
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithCustomTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.foo.html");
        $this->assertEquals([], $view->getVars());
    }

    /**
     * Tests registering a builder with the same filename as a view, but resolves to a different view file
     */
    public function testRegisteringBuilderWithSameFilenameAsViewButResolvesToDifferentViewFile()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.foo", function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->at(0))
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->viewNameResolver->expects($this->at(0))
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithCustomTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.bar");
        $this->assertEquals([], $view->getVars());
    }

    /**
     * Tests registering a builder for a view without an extension and then creating that view with an extension
     */
    public function testRegisteringBuilderWithoutExtensionAndCreatingSameViewWithExtension()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $this->assertEquals("bar", $view->getVar("foo"));
    }

    /**
     * Tests registering a closure builder
     */
    public function testRegisteringClosureBuilder()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function (IView $view) {
            $view->setVar("foo", "bar");

            return $view;
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
    }

    /**
     * Tests registering multiple builders
     */
    public function testRegisteringMultipleBuilders()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function (IView $view) {
            return (new FooBuilder())->build($view);
        });
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters", function (IView $view) {
            return (new BarBuilder())->build($view);
        });
        $this->viewNameResolver->expects($this->any())
            ->method("resolve")
            ->willReturn(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters");
        $this->assertEquals("bar", $view->getVar("foo"));
        $this->assertEquals("baz", $view->getVar("bar"));
    }
}