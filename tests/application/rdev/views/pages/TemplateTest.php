<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template class
 */
namespace RDev\Views\Pages;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template */
    const TEMPLATE_PATH = "/templates/Test.html";

    /**
     * Tests getting the output
     */
    public function testGettingOutput()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $template->getOutput());
    }

    /**
     * Tests getting the output for a template whose tags we didn't set
     */
    public function testGettingOutputForTemplateWithUnsetTags()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH);
        $this->assertEquals(", !", $template->getOutput());
    }

    /**
     * Tests getting the output by setting the template path in the constructor
     */
    public function testGettingOutputLBySpecifyingTemplatePathInConstructor()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $template->getOutput());
    }

    /**
     * Tests setting multiple tags in a template
     */
    public function testSettingMultipleTags()
    {
        $template = new Template();
        $template->setTags(["foo" => "bar", "abc" => "xyz"]);
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($template);
        $this->assertEquals(["%%foo%%" => "bar", "%%abc%%" => "xyz"], $tags);
    }

    /**
     * Tests setting a tag in a template
     */
    public function testSettingSingleTag()
    {
        $template = new Template();
        $template->setTag("foo", "bar");
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($template);
        $this->assertEquals(["%%foo%%" => "bar"], $tags);
    }

    /**
     * Tests setting the template path
     */
    public function testSettingTemplatePath()
    {
        $template = new Template();
        $template->setTemplatePath("foo");
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("templatePath");
        $property->setAccessible(true);
        $templatePath = $property->getValue($template);
        $this->assertEquals("foo", $templatePath);
    }
} 