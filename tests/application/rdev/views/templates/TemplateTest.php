<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template class
 */
namespace RDev\Views\Templates;
use RDev\Models\Files;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tags */
    const TEMPLATE_PATH_WITH_DEFAULT_TAGS = "/files/TestWithDefaultTags.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_INVALID_PHP_CODE = "/files/TestWithInvalidPHP.html";

    /** @var Template The template to use in the tests */
    private $template = null;
    /** @var Files\FileSystem The file system used to read templates */
    private $fileSystem = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->template = new Template();
        $this->fileSystem = new Files\FileSystem();
    }

    /**
     * Tests getting the escaped tags
     */
    public function testGettingEscapedTags()
    {
        $this->assertEquals(Template::DEFAULT_ESCAPED_OPEN_TAG, $this->template->getEscapedOpenTag());
        $this->assertEquals(Template::DEFAULT_ESCAPED_CLOSE_TAG, $this->template->getEscapedCloseTag());
        $this->template->setEscapedOpenTag("foo");
        $this->template->setEscapedCloseTag("bar");
        $this->assertEquals("foo", $this->template->getEscapedOpenTag());
        $this->assertEquals("bar", $this->template->getEscapedCloseTag());
    }

    /**
     * Tests getting a non-existent tag
     */
    public function testGettingNonExistentTag()
    {
        $this->assertNull($this->template->getTag("foo"));
    }

    /**
     * Tests getting a non-existent variable
     */
    public function testGettingNonExistentVariable()
    {
        $this->assertNull($this->template->getVar("foo"));
    }

    /**
     * Tests getting a tag
     */
    public function testGettingTag()
    {
        $this->template->setTag("foo", "bar");
        $this->assertEquals("bar", $this->template->getTag("foo"));
    }

    /**
     * Tests getting the tags
     */
    public function testGettingTags()
    {
        $this->template->setTag("foo", "bar");
        $this->assertEquals(["foo" => "bar"], $this->template->getTags());
    }

    /**
     * Tests getting the unescaped tags
     */
    public function testGettingUnescapedTags()
    {
        $this->assertEquals(Template::DEFAULT_UNESCAPED_OPEN_TAG, $this->template->getUnescapedOpenTag());
        $this->assertEquals(Template::DEFAULT_UNESCAPED_CLOSE_TAG, $this->template->getUnescapedCloseTag());
        $this->template->setUnescapedOpenTag("foo");
        $this->template->setUnescapedCloseTag("bar");
        $this->assertEquals("foo", $this->template->getUnescapedOpenTag());
        $this->assertEquals("bar", $this->template->getUnescapedCloseTag());
    }

    /**
     * Tests getting a var
     */
    public function testGettingVar()
    {
        $this->template->setVar("foo", "bar");
        $this->assertEquals("bar", $this->template->getVar("foo"));
    }

    /**
     * Tests getting the bars
     */
    public function testGettingVars()
    {
        $this->template->setVar("foo", "bar");
        $this->assertEquals(["foo" => "bar"], $this->template->getVars());
    }

    /**
     * Tests not setting the contents in the constructor
     */
    public function testNotSettingContentsInConstructor()
    {
        $this->assertEmpty($this->template->getContents());
    }

    /**
     * Tests setting the contents
     */
    public function testSettingContents()
    {
        $this->template->setContents("blah");
        $this->assertEquals("blah", $this->template->getContents());
    }

    /**
     * Tests setting the contents in the constructor
     */
    public function testSettingContentsInConstructor()
    {
        $template = new Template("foo");
        $this->assertEquals("foo", $template->getContents());
    }

    /**
     * Tests setting the contents to a non-string
     */
    public function testSettingContentsToNonString()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->template->setContents(["Not a string"]);
    }

    /**
     * Tests setting multiple tags in a template
     */
    public function testSettingMultipleTags()
    {
        $this->template->setTags(["foo" => "bar", "abc" => "xyz"]);
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar", "abc" => "xyz"], $tags);
    }

    /**
     * Tests setting multiple variables in a template
     */
    public function testSettingMultipleVariables()
    {
        $this->template->setVars(["foo" => "bar", "abc" => ["xyz"]]);
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar", "abc" => ["xyz"]], $vars);
    }

    /**
     * Tests setting a tag in a template
     */
    public function testSettingSingleTag()
    {
        $this->template->setTag("foo", "bar");
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("tags");
        $property->setAccessible(true);
        $tags = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar"], $tags);
    }

    /**
     * Tests setting a variable in a template
     */
    public function testSettingSingleVariable()
    {
        $this->template->setVar("foo", "bar");
        $reflectionObject = new \ReflectionObject($this->template);
        $property = $reflectionObject->getProperty("vars");
        $property->setAccessible(true);
        $vars = $property->getValue($this->template);
        $this->assertEquals(["foo" => "bar"], $vars);
    }

    /**
     * Tests that nothing is output from an invalid template
     */
    public function testThatNothingIsOutputFromInvalidTemplate()
    {
        $output = "";
        $startOBLevel = ob_get_level();

        try
        {
            $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_INVALID_PHP_CODE);
            $this->template->setContents($contents);
        }
        catch(\RuntimeException $ex)
        {
            // Don't do anything
        }
        finally
        {
            while(ob_get_level() > $startOBLevel)
            {
                $output .= ob_get_clean();
            }
        }

        $this->assertEmpty($output);
    }
} 