<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template factory
 */
namespace RDev\Views;
use RDev\Files;
use RDev\Tests\Views\Mocks;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Files\FileSystem The file system to use in tests */
    private $fileSystem = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new Files\FileSystem();
    }

    /**
     * Tests passing in a root directory with a trailing slash
     */
    public function testPassingInRootWithTrailingSlash()
    {
        $factory = new Factory($this->fileSystem, __DIR__ . "/files/");
        $template = $factory->createTemplate("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a root directory without a trailing slash
     */
    public function testPassingInRootWithoutTrailingSlash()
    {
        $factory = new Factory($this->fileSystem, __DIR__ . "/files");
        $template = $factory->createTemplate("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a template path that does not exist
     */
    public function testPassingInTemplatePathThatDoesNotExist()
    {
        $this->setExpectedException("RDev\\Files\\FileSystemException");
        $factory = new Factory($this->fileSystem, __DIR__ . "/files");
        $factory->createTemplate("doesNotExist.html");
    }

    /**
     * Tests passing in a template path with a preceding slash
     */
    public function testPassingInTemplatePathWithPrecedingSlash()
    {
        $factory = new Factory($this->fileSystem, __DIR__ . "/files");
        $template = $factory->createTemplate("/TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a template path without a preceding slash
     */
    public function testPassingInTemplatePathWithoutPrecedingSlash()
    {
        $factory = new Factory($this->fileSystem, __DIR__ . "/files");
        $template = $factory->createTemplate("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests registering a builder
     */
    public function testsRegisteringBuilder()
    {
        $factory = new Factory($this->fileSystem, __DIR__ . "/files");
        $factory->registerBuilder("TestWithDefaultTags.html", new Mocks\FooBuilder());
        $template = $factory->createTemplate("TestWithDefaultTags.html");
        $this->assertEquals("bar", $template->getTag("foo"));
    }

    /**
     * Tests registering multiple builders
     */
    public function testsRegisteringMultipleBuilders()
    {
        $factory = new Factory($this->fileSystem, __DIR__ . "/files");
        $factory->registerBuilder("TestWithDefaultTags.html", new Mocks\FooBuilder());
        $factory->registerBuilder("TestWithDefaultTags.html", new Mocks\BarBuilder());
        $template = $factory->createTemplate("TestWithDefaultTags.html");
        $this->assertEquals("bar", $template->getTag("foo"));
        $this->assertEquals("baz", $template->getTag("bar"));
    }
}