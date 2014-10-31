<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template factory
 */
namespace RDev\Views\Templates;
use RDev\Files;

class TemplateFactoryTest extends \PHPUnit_Framework_TestCase
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
        $factory = new TemplateFactory($this->fileSystem, __DIR__ . "/files/");
        $template = $factory->create("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Templates\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a root directory without a trailing slash
     */
    public function testPassingInRootWithoutTrailingSlash()
    {
        $factory = new TemplateFactory($this->fileSystem, __DIR__ . "/files");
        $template = $factory->create("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Templates\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a template path that does not exist
     */
    public function testPassingInTemplatePathThatDoesNotExist()
    {
        $this->setExpectedException("RDev\\Files\\FileSystemException");
        $factory = new TemplateFactory($this->fileSystem, __DIR__ . "/files");
        $factory->create("doesNotExist.html");
    }

    /**
     * Tests passing in a template path with a preceding slash
     */
    public function testPassingInTemplatePathWithPrecedingSlash()
    {
        $factory = new TemplateFactory($this->fileSystem, __DIR__ . "/files");
        $template = $factory->create("/TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Templates\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }

    /**
     * Tests passing in a template path without a preceding slash
     */
    public function testPassingInTemplatePathWithoutPrecedingSlash()
    {
        $factory = new TemplateFactory($this->fileSystem, __DIR__ . "/files");
        $template = $factory->create("TestWithDefaultTags.html");
        $expectedContent = $this->fileSystem->read(__DIR__ . "/files/TestWithDefaultTags.html");
        $this->assertInstanceOf("RDev\\Views\\Templates\\Template", $template);
        $this->assertEquals($expectedContent, $template->getContents());
    }
}