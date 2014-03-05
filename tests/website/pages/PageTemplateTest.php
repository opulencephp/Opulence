<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the page template class
 */
namespace RamODev\Website\Pages;

class PageTemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template */
    const TEMPLATE_PATH = "/templates/Test.html";

    /**
     * Tests setting multiple tags in a template
     */
    public function testSettingMultipleTag()
    {
        $page = new PageTemplate();
        $page->setTags(array("foo" => "bar", "abc" => "xyz"));
        $reflectionObject = new \ReflectionObject($page);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($page);
        $this->assertEquals(array("%%foo%%" => "bar", "%%abc%%" => "xyz"), $tags);
    }

    /**
     * Tests setting a tag in a template
     */
    public function testSettingSingleTag()
    {
        $page = new PageTemplate();
        $page->setTag("foo", "bar");
        $reflectionObject = new \ReflectionObject($page);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($page);
        $this->assertEquals(array("%%foo%%" => "bar"), $tags);
    }

    /**
     * Tests setting the template path
     */
    public function testSettingTemplatePath()
    {
        $page = new PageTemplate();
        $page->setTemplatePath("foo");
        $reflectionObject = new \ReflectionObject($page);
        $property = $reflectionObject->getProperty("templatePath");
        $property->setAccessible(true);
        $templatePath = $property->getValue($page);
        $this->assertEquals("foo", $templatePath);
    }

    /**
     * Tests getting the HTML
     */
    public function testGettingHTML()
    {
        $page = new PageTemplate();
        $page->setTemplatePath(__DIR__ . self::TEMPLATE_PATH);
        $page->setTag("foo", "Hello");
        $page->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $page->getHTML());
    }
} 