<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template factory
 */
namespace RDev\Views\Factories;
use RDev\Files;
use RDev\Tests\Views\Mocks;

class TemplateFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Files\FileSystem The file system to use in tests */
    private $fileSystem = null;
    /** @var TemplateFactory The template factory to use in tests */
    private $templateFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new Files\FileSystem();
        $this->templateFactory = new TemplateFactory($this->fileSystem, __DIR__ . "/../files");
    }

    /**
     * Tests aliasing a template path
     */
    public function testAlias()
    {
        $this->templateFactory->alias("foo", "TestWithDefaultTags.html");
        $this->assertEquals(
            $this->templateFactory->create("foo"),
            $this->templateFactory->create("TestWithDefaultTags.html")
        );
    }

    /**
     * Tests passing in a root directory with a trailing slash
     */
    public function testPassingInRootWithTrailingSlash()
    {
        $template = $this->templateFactory->create("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a root directory without a trailing slash
     */
    public function testPassingInRootWithoutTrailingSlash()
    {
        $template = $this->templateFactory->create("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a template path that does not exist
     */
    public function testPassingInTemplatePathThatDoesNotExist()
    {
        $this->setExpectedException("RDev\\Files\\FileSystemException");
        $this->templateFactory->create("doesNotExist.html");
    }

    /**
     * Tests passing in a template path with a preceding slash
     */
    public function testPassingInTemplatePathWithPrecedingSlash()
    {
        $template = $this->templateFactory->create("/TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a template path without a preceding slash
     */
    public function testPassingInTemplatePathWithoutPrecedingSlash()
    {
        $template = $this->templateFactory->create("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/../files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests registering a builder to an alias
     */
    public function testRegisteringBuilderToAlias()
    {
        $this->templateFactory->alias("foo", "TestWithDefaultTags.html");
        $this->templateFactory->registerBuilder("foo", function ()
        {
            return new Mocks\FooBuilder();
        });
        $template = $this->templateFactory->create("foo");
        $this->assertEquals("bar", $template->getTag("foo"));
    }

    /**
     * Tests registering builders to mix of paths and aliases
     */
    public function testRegisteringBuilderToMixOfPathsAndAliases()
    {
        $this->templateFactory->alias("foo", "TestWithDefaultTags.html");
        $this->templateFactory->registerBuilder(["foo", "TestWithCustomTags.html"], function()
        {
            return new Mocks\FooBuilder();
        });
        $fooTemplate = $this->templateFactory->create("foo");
        $customTagTemplate = $this->templateFactory->create("TestWithCustomTags.html");
        $this->assertEquals("bar", $fooTemplate->getTag("foo"));
        $this->assertEquals("bar", $customTagTemplate->getTag("foo"));
    }

    /**
     * Tests registering builders to multiple aliases
     */
    public function testRegisteringBuilderToMultipleAliases()
    {
        $this->templateFactory->alias("foo", "TestWithDefaultTags.html");
        $this->templateFactory->alias("bar", "TestWithCustomTags.html");
        $this->templateFactory->registerBuilder(["foo", "bar"], function()
        {
            return new Mocks\FooBuilder();
        });
        $fooTemplate = $this->templateFactory->create("foo");
        $barTemplate = $this->templateFactory->create("bar");
        $this->assertEquals("bar", $fooTemplate->getTag("foo"));
        $this->assertEquals("bar", $barTemplate->getTag("foo"));
    }

    /**
     * Tests registering builders to multiple paths
     */
    public function testRegisteringBuilderToMultiplePaths()
    {
        $this->templateFactory->registerBuilder(["TestWithDefaultTags.html", "TestWithCustomTags.html"], function()
        {
            return new Mocks\FooBuilder();
        });
        $defaultTagsTemplate = $this->templateFactory->create("TestWithDefaultTags.html");
        $customTagsTemplate = $this->templateFactory->create("TestWithCustomTags.html");
        $this->assertEquals("bar", $defaultTagsTemplate->getTag("foo"));
        $this->assertEquals("bar", $customTagsTemplate->getTag("foo"));
    }

    /**
     * Tests registering a builder to a path also registers to an alias
     */
    public function testRegisteringBuilderToPathAlsoRegistersToAlias()
    {
        $this->templateFactory->alias("foo", "TestWithDefaultTags.html");
        $this->templateFactory->registerBuilder("TestWithDefaultTags.html", function ()
        {
            return new Mocks\FooBuilder();
        });
        $template = $this->templateFactory->create("foo");
        $this->assertEquals("bar", $template->getTag("foo"));
    }

    /**
     * Tests registering a builder
     */
    public function testsRegisteringBuilder()
    {
        $this->templateFactory->registerBuilder("TestWithDefaultTags.html", function ()
        {
            return new Mocks\FooBuilder();
        });
        $template = $this->templateFactory->create("TestWithDefaultTags.html");
        $this->assertEquals("bar", $template->getTag("foo"));
    }

    /**
     * Tests registering multiple builders
     */
    public function testsRegisteringMultipleBuilders()
    {
        $this->templateFactory->registerBuilder("TestWithDefaultTags.html", function ()
            {
                return new Mocks\FooBuilder();
            });
        $this->templateFactory->registerBuilder("TestWithDefaultTags.html", function ()
            {
                return new Mocks\BarBuilder();
            });
        $template = $this->templateFactory->create("TestWithDefaultTags.html");
        $this->assertEquals("bar", $template->getTag("foo"));
        $this->assertEquals("baz", $template->getTag("bar"));
    }
}