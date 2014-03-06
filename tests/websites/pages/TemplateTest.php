<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template class
 */
namespace RamODev\Websites\Pages;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template */
    const TEMPLATE_PATH = "/templates/Test.html";

    /**
     * Tests getting the HTML by setting the template path in the constructor
     */
    public function testGettingHTMLBySpecifyingTemplatePathInConstructor()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $template->getHTML());
    }

    /**
     * Tests getting the HTML
     */
    public function testGettingHTML()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $template->getHTML());
    }

    /**
     * Tests getting the HTML for a template whose tags we didn't set
     */
    public function testGettingHTMLForTemplateWithUnsetTags()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH);
        $this->assertEquals(", !", $template->getHTML());
    }

    /**
     * Tests setting multiple tags in a template
     */
    public function testSettingMultipleTag()
    {
        $template = new Template();
        $template->setTags(array("foo" => "bar", "abc" => "xyz"));
        $reflectionObject = new \ReflectionObject($template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($template);
        $this->assertEquals(array("%%foo%%" => "bar", "%%abc%%" => "xyz"), $tags);
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
        $this->assertEquals(array("%%foo%%" => "bar"), $tags);
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