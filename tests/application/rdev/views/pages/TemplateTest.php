<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template class
 */
namespace RDev\Views\Pages;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tag placeholders */
    const TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS = "/templates/TestWithDefaultTagPlaceholders.html";
    /** The path to the test template with custom tag placeholders */
    const TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS = "/templates/TestWithCustomTagPlaceholders.html";

    /**
     * Tests getting the close tag when we've set it to a custom value
     */
    public function testGettingCustomCloseTag()
    {
        $template = new Template();
        $closeTag = "$$";
        $template->setCloseTagPlaceholder($closeTag);
        $this->assertEquals($closeTag, $template->getCloseTagPlaceholder());
    }

    /**
     * Tests getting the open tag when we've set it to a custom value
     */
    public function testGettingCustomOpenTag()
    {
        $template = new Template();
        $openTag = "^^";
        $template->setOpenTagPlaceholder($openTag);
        $this->assertEquals($openTag, $template->getOpenTagPlaceholder());
    }

    /**
     * Tests getting the close tag when it's set to the default value
     */
    public function testGettingDefaultCloseTag()
    {
        $template = new Template();
        $this->assertEquals($template::DEFAULT_CLOSE_TAG_PLACEHOLDER, $template->getCloseTagPlaceholder());
    }

    /**
     * Tests getting the open tag when it's set to the default value
     */
    public function testGettingDefaultOpenTag()
    {
        $template = new Template();
        $this->assertEquals($template::DEFAULT_OPEN_TAG_PLACEHOLDER, $template->getOpenTagPlaceholder());
    }

    /**
     * Tests getting the output for a template whose tags we didn't set
     */
    public function testGettingOutputForTemplateWithUnsetTags()
    {
        $template = new Template();
        $template->setTemplatePath(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $this->assertEquals(", !", $template->getOutput());
    }

    /**
     * Tests getting the output by setting the template path in the constructor
     */
    public function testGettingOutputLBySpecifyingTemplatePathInConstructor()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $template->getOutput());
    }

    /**
     * Tests getting the output with a template that uses custom tag placeholders
     */
    public function testGettingOutputOfTemplateWithCustomTagPlaceholders()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS);
        $template->setOpenTagPlaceholder("^^");
        $template->setCloseTagPlaceholder("$$");
        $template->setTag("foo", "Hello");
        $template->setTag("bar", "world");
        $this->assertEquals("Hello, world!", $template->getOutput());
    }

    /**
     * Tests getting the output with a template that uses the default tag placeholders
     */
    public function testGettingOutputOfTemplateWithDefaultTagPlaceholders()
    {
        $template = new Template(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
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
        $this->assertEquals(["foo" => "bar", "abc" => "xyz"], $tags);
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
        $this->assertEquals(["foo" => "bar"], $tags);
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