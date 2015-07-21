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
use Opulence\Views\FortuneView;

class ViewFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileSystem The file system to use in tests */
    private $fileSystem = null;
    /** @var ViewFactory The view factory to use in tests */
    private $viewFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->markTestSkipped();
        $this->fileSystem = new FileSystem();
        $this->viewFactory = new ViewFactory($this->fileSystem, __DIR__ . "/../files");
    }

    /**
     * Tests aliasing a view path
     */
    public function testAlias()
    {
        $this->viewFactory->alias("foo", "TestWithDefaultTagDelimiters.html");
        $this->assertEquals(
            $this->viewFactory->create("foo"),
            $this->viewFactory->create("TestWithDefaultTagDelimiters.html")
        );
    }

    /**
     * Tests passing in a root directory with a trailing slash
     */
    public function testPassingInRootWithTrailingSlash()
    {
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->assertInstanceOf(FortuneView::class, $view);
        $this->assertEquals($expectedContent, $view->getContents());
    }

    /**
     * Tests passing in a root directory without a trailing slash
     */
    public function testPassingInRootWithoutTrailingSlash()
    {
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->assertInstanceOf(FortuneView::class, $view);
        $this->assertEquals($expectedContent, $view->getContents());
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
        $view = $this->viewFactory->create("/TestWithDefaultTagDelimiters.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->assertInstanceOf(FortuneView::class, $view);
        $this->assertEquals($expectedContent, $view->getContents());
    }

    /**
     * Tests passing in a view path without a preceding slash
     */
    public function testPassingInViewPathWithoutPrecedingSlash()
    {
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTagDelimiters.html");
        $this->assertInstanceOf(FortuneView::class, $view);
        $this->assertEquals($expectedContent, $view->getContents());
    }

    /**
     * Tests registering a builder
     */
    public function testRegisteringBuilder()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.html", function ()
        {
            return new FooBuilder();
        });
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $this->assertEquals("bar", $view->getTag("foo"));
    }

    /**
     * Tests registering a builder to an alias
     */
    public function testRegisteringBuilderToAlias()
    {
        $this->viewFactory->alias("foo", "TestWithDefaultTagDelimiters.html");
        $this->viewFactory->registerBuilder("foo", function ()
        {
            return new FooBuilder();
        });
        $view = $this->viewFactory->create("foo");
        $this->assertEquals("bar", $view->getTag("foo"));
    }

    /**
     * Tests registering builders to mix of paths and aliases
     */
    public function testRegisteringBuilderToMixOfPathsAndAliases()
    {
        $this->viewFactory->alias("foo", "TestWithDefaultTagDelimiters.html");
        $this->viewFactory->registerBuilder(["foo", "TestWithCustomTagDelimiters.html"], function ()
        {
            return new FooBuilder();
        });
        $fooView = $this->viewFactory->create("foo");
        $customTagView = $this->viewFactory->create("TestWithCustomTagDelimiters.html");
        $this->assertEquals("bar", $fooView->getTag("foo"));
        $this->assertEquals("bar", $customTagView->getTag("foo"));
    }

    /**
     * Tests registering builders to multiple aliases
     */
    public function testRegisteringBuilderToMultipleAliases()
    {
        $this->viewFactory->alias("foo", "TestWithDefaultTagDelimiters.html");
        $this->viewFactory->alias("bar", "TestWithCustomTagDelimiters.html");
        $this->viewFactory->registerBuilder(["foo", "bar"], function ()
        {
            return new FooBuilder();
        });
        $fooView = $this->viewFactory->create("foo");
        $barView = $this->viewFactory->create("bar");
        $this->assertEquals("bar", $fooView->getTag("foo"));
        $this->assertEquals("bar", $barView->getTag("foo"));
    }

    /**
     * Tests registering builders to multiple paths
     */
    public function testRegisteringBuilderToMultiplePaths()
    {
        $this->viewFactory->registerBuilder(["TestWithDefaultTagDelimiters.html", "TestWithCustomTagDelimiters.html"], function ()
        {
            return new FooBuilder();
        });
        $defaultTagsView = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $customTagsView = $this->viewFactory->create("TestWithCustomTagDelimiters.html");
        $this->assertEquals("bar", $defaultTagsView->getTag("foo"));
        $this->assertEquals("bar", $customTagsView->getTag("foo"));
    }

    /**
     * Tests registering a builder to a path also registers to an alias
     */
    public function testRegisteringBuilderToPathAlsoRegistersToAlias()
    {
        $this->viewFactory->alias("foo", "TestWithDefaultTagDelimiters.html");
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.html", function ()
        {
            return new FooBuilder();
        });
        $view = $this->viewFactory->create("foo");
        $this->assertEquals("bar", $view->getTag("foo"));
    }

    /**
     * Tests registering multiple builders
     */
    public function testRegisteringMultipleBuilders()
    {
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.html", function ()
        {
            return new FooBuilder();
        });
        $this->viewFactory->registerBuilder("TestWithDefaultTagDelimiters.html", function ()
        {
            return new BarBuilder();
        });
        $view = $this->viewFactory->create("TestWithDefaultTagDelimiters.html");
        $this->assertEquals("bar", $view->getTag("foo"));
        $this->assertEquals("baz", $view->getTag("bar"));
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