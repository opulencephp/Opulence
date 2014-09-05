<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template class
 */
namespace RDev\Views\Templates;
use RDev\Tests\Models\Mocks;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tag placeholders */
    const TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS = "/files/TestWithDefaultTagPlaceholders.html";
    /** The path to the test template with custom tag placeholders */
    const TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS = "/files/TestWithCustomTagPlaceholders.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_PHP_CODE = "/files/TestWithPHP.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_INVALID_PHP_CODE = "/files/TestWithInvalidPHP.html";

    /** @var Template The template to use in the tests */
    private $template = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->template = new Template(new Compiler());
    }

    /**
     * Tests getting the compiler
     */
    public function testGettingCompiler()
    {
        $compiler = new Compiler();
        $this->template->setCompiler($compiler);
        $this->assertSame($compiler, $this->template->getCompiler());
    }

    /**
     * Tests getting the close tag when we've set it to a custom value
     */
    public function testGettingCustomCloseTag()
    {
        $closeTag = "$$";
        $this->template->setCloseTagPlaceholder($closeTag);
        $this->assertEquals($closeTag, $this->template->getCloseTagPlaceholder());
    }

    /**
     * Tests getting the open tag when we've set it to a custom value
     */
    public function testGettingCustomOpenTag()
    {
        $openTag = "^^";
        $this->template->setOpenTagPlaceholder($openTag);
        $this->assertEquals($openTag, $this->template->getOpenTagPlaceholder());
    }

    /**
     * Tests getting the close tag when it's set to the default value
     */
    public function testGettingDefaultCloseTag()
    {
        $this->assertEquals(Template::DEFAULT_CLOSE_TAG_PLACEHOLDER, $this->template->getCloseTagPlaceholder());
    }

    /**
     * Tests getting the open tag when it's set to the default value
     */
    public function testGettingDefaultOpenTag()
    {
        $this->assertEquals(Template::DEFAULT_OPEN_TAG_PLACEHOLDER, $this->template->getOpenTagPlaceholder());
    }

    /**
     * Tests getting the unrendered template from a file
     */
    public function testGettingUnrenderedTemplateFromAFile()
    {
        $templatePath = __DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS;
        $unrenderedTemplate = file_get_contents($templatePath);
        $this->template->readFromFile($templatePath);
        $this->assertEquals($unrenderedTemplate, $this->template->getUnrenderedTemplate());
    }

    /**
     * Tests getting the unrendered template from input
     */
    public function testGettingUnrenderedTemplateFromInput()
    {
        $unrenderedTemplate = "Hello, {{username}}";
        $this->template->readFromInput($unrenderedTemplate);
        $this->assertEquals($unrenderedTemplate, $this->template->getUnrenderedTemplate());
    }

    /**
     * Tests reading from input that isn't a string
     */
    public function testReadFromInputThatIsNotString()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->template->readFromInput(["Not a string"]);
    }

    /**
     * Tests reading from an invalid path
     */
    public function testReadingFromInvalidPath()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->template->readFromFile("PATH_THAT_DOES_NOT_EXIST.txt");
    }

    /**
     * Tests reading from a path that isn't a string
     */
    public function testReadingFromPathThatIsNotString()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->template->readFromFile(["Not a string"]);
    }

    /**
     * Tests rendering invalid PHP
     */
    public function testRenderingInvalidPHP()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->template->readFromFile(__DIR__ . "/files/TestWithInvalidPHP.html");
        $this->template->render();
    }

    /**
     * Tests rendering a template that uses custom tag placeholders
     */
    public function testRenderingTemplateWithCustomTagPlaceholders()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS);
        $this->template->setOpenTagPlaceholder("^^");
        $this->template->setCloseTagPlaceholder("$$");
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertEquals("Hello, world! ^^blah$$. a&amp;b. me too. c&amp;d. {{{\"e&f\"}}}. {{{ \"g&h\" }}}. {{{blah}}}. Today is $functionResult.",
            $this->template->render());
    }

    /**
     * Tests rendering a template that uses the default tag placeholders
     */
    public function testRenderingTemplateWithDefaultTagPlaceholders()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertEquals("Hello, world! {{blah}}. a&amp;b. me too. c&amp;d. {{{\"e&f\"}}}. {{{ \"g&h\" }}}. {{{blah}}}. Today is $functionResult.",
            $this->template->render());
    }

    /**
     * Tests rendering a template with PHP code
     */
    public function testRenderingTemplateWithPHPCode()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_PHP_CODE);
        $user1 = new Mocks\User(1, "foo");
        $user2 = new Mocks\User(2, "bar");
        $this->template->setTag("listDescription", "usernames");
        $this->template->setVar("users", [$user1, $user2]);
        $this->template->setVar("coolestGuy", "Dave");
        $functionResult = $this->registerFunction();
        $this->assertEquals('TEST List of usernames on ' . $functionResult . ':
<ul>
    <li>foo</li><li>bar</li></ul> 2 items
<br>Dave is a pretty cool guy. Alternative syntax works! I agree. Fake closing PHP tag: ?>', $this->template->render());
    }

    /**
     * Tests rendering a template whose custom tags we didn't set
     */
    public function testRenderingTemplateWithUnsetCustomTags()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_PLACEHOLDERS);
        $this->template->setOpenTagPlaceholder("^^");
        $this->template->setCloseTagPlaceholder("$$");
        $functionResult = $this->registerFunction();
        $this->assertEquals(", ! ^^blah$$. . me too. c&amp;d. {{{\"e&f\"}}}. {{{ \"g&h\" }}}. {{{blah}}}. Today is $functionResult.", $this->template->render());
    }

    /**
     * Tests rendering a template whose tags we didn't set
     */
    public function testRenderingTemplateWithUnsetTags()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_PLACEHOLDERS);
        $functionResult = $this->registerFunction();
        $this->assertEquals(", ! {{blah}}. . me too. c&amp;d. {{{\"e&f\"}}}. {{{ \"g&h\" }}}. {{{blah}}}. Today is $functionResult.", $this->template->render());
    }

    /**
     * Tests that we cannot set the close then the open tags to the same thing as the safe tags
     */
    public function testSettingCloseThenOpenTagsToSafeTags()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->template->setCloseTagPlaceholder(Template::SAFE_CLOSE_TAG_PLACEHOLDER);
        $this->template->setOpenTagPlaceholder(Template::SAFE_OPEN_TAG_PLACEHOLDER);
    }

    /**
     * Tests setting the compiler in the constructor
     */
    public function testSettingCompilerInConstructor()
    {
        $compiler = new Compiler();
        $template = new Template($compiler);
        $this->assertSame($compiler, $template->getCompiler());
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
     * Tests that we cannot set the open then the close tags to the same thing as the safe tags
     */
    public function testSettingOpenThenCloseTagsToSafeTags()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->template->setOpenTagPlaceholder(Template::SAFE_OPEN_TAG_PLACEHOLDER);
        $this->template->setCloseTagPlaceholder(Template::SAFE_CLOSE_TAG_PLACEHOLDER);
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
     * Registers a function to the template for use in testing
     *
     * @return string The expected result of the compiler
     */
    private function registerFunction()
    {
        $this->template->registerFunction("date", function (\DateTime $date, $format, array $someArray)
        {
            echo $date->format($format) . " and count of array is " . count($someArray);
        });
        $today = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->template->setVar("today", $today);

        return $today->format("m/d/Y") . " and count of array is 3";
    }
} 