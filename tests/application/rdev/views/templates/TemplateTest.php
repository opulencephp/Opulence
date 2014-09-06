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
     * Tests the built-in absolute value function
     */
    public function testBuiltInAbsFunction()
    {
        $number = -3.9;
        $this->template->setVar("number", $number);
        $this->template->readFromInput('{{abs($number)}}');
        $this->assertEquals(abs($number), $this->template->render());
    }

    /**
     * Tests the built-in ceiling function
     */
    public function testBuiltInCeilFunction()
    {
        $number = 3.9;
        $this->template->setVar("number", $number);
        $this->template->readFromInput('{{ceil($number)}}');
        $this->assertEquals(ceil($number), $this->template->render());
    }

    /**
     * Tests the built-in count function
     */
    public function testBuiltInCountFunction()
    {
        $array = [1, 2, 3];
        $this->template->setVar("array", $array);
        $this->template->readFromInput('{{count($array)}}');
        $this->assertEquals(count($array), $this->template->render());
    }

    /**
     * Tests the built-in date function
     */
    public function testBuiltInDateFunction()
    {
        $today = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->template->setVar("today", $today);
        $this->template->readFromInput('{{date($today)}}');
        $this->template->setVar("today", $today);
        // Test with date parameter
        $this->assertSame($today->format("m/d/Y"), $this->template->render());
        // Test with date and format parameters
        $format = "Y-m-d";
        $this->template->readFromInput('{{date($today, "' . $format . '")}}');
        $this->assertSame($today->format($format), $this->template->render());
        // Test with date, format, and timezone parameters
        $format = "Y-m-d";
        $timezone = new \DateTimeZone("America/New_York");
        $today->setTimezone($timezone);
        $this->template->setVar("timezone", $timezone);
        $this->template->readFromInput('{{date($today, "' . $format . '", $timezone)}}');
        $this->assertSame($today->format($format), $this->template->render());
        // Test an invalid timezone
        $this->template->setVar("timezone", []);
        $this->template->readFromInput('{{date($today, "' . $format . '", $timezone)}}');
        $this->assertSame($today->format($format), $this->template->render());
    }

    /**
     * Tests the built-in floor function
     */
    public function testBuiltInFloorFunction()
    {
        $number = 3.9;
        $this->template->setVar("number", $number);
        $this->template->readFromInput('{{floor($number)}}');
        $this->assertEquals(floor($number), $this->template->render());
    }

    /**
     * Tests the built-in implode function
     */
    public function testBuiltInImplodeFunction()
    {
        $array = [1, 2, 3];
        $this->template->setVar("array", $array);
        $this->template->readFromInput('{{implode(",", $array)}}');
        $this->assertEquals(implode(",", $array), $this->template->render());
    }

    /**
     * Tests the built-in JSON encode function
     */
    public function testBuiltInJSONEncodeFunction()
    {
        $array = ["foo" => ["bar" => "blah"]];
        $this->template->setVar("array", $array);
        // Test with value parameter
        $this->template->readFromInput('{{json_encode($array)}}');
        $this->assertEquals(json_encode($array), $this->template->render());
        // Test with value and options parameters
        $this->template->setVar("options", JSON_HEX_TAG);
        $this->template->readFromInput('{{json_encode($array, $options)}}');
        $this->assertEquals(json_encode($array, JSON_HEX_TAG), $this->template->render());
        // Test with value, options, and depth parameters
        $this->template->setVar("depth", 1);
        $this->template->readFromInput('{{json_encode($array, $options, $depth)}}');
        $this->assertEquals(json_encode($array, JSON_HEX_TAG, 1), $this->template->render());
    }

    /**
     * Tests the built-in lowercase first function
     */
    public function testBuiltInLCFirstFunction()
    {
        $this->template->setVar("string", "FOO BAR");
        $this->template->readFromInput('{{lcfirst($string)}}');
        $this->assertEquals(lcfirst("FOO BAR"), $this->template->render());
    }

    /**
     * Tests the built-in round function
     */
    public function testBuiltInRoundFunction()
    {
        $number = 3.85;
        $this->template->setVar("number", $number);
        // Test with number parameter
        $this->template->readFromInput('{{round($number)}}');
        $this->assertEquals(round($number), $this->template->render());
        // Test with number and precision parameters
        $this->template->readFromInput('{{round($number, 1)}}');
        $this->assertEquals(round($number, 1), $this->template->render());
        // Test with number, precision, and mode parameters
        $this->template->readFromInput('{{round($number, 0, PHP_ROUND_HALF_DOWN)}}');
        $this->assertEquals(round($number, 0, PHP_ROUND_HALF_DOWN), $this->template->render());
    }

    /**
     * Tests the built-in lowercase function
     */
    public function testBuiltInStrToLowerFunction()
    {
        $this->template->setVar("string", "FOO BAR");
        $this->template->readFromInput('{{strtolower($string)}}');
        $this->assertEquals(strtolower("FOO BAR"), $this->template->render());
    }

    /**
     * Tests the built-in uppercase function
     */
    public function testBuiltInStrToUpperFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->readFromInput('{{strtoupper($string)}}');
        $this->assertEquals(strtoupper("foo bar"), $this->template->render());
    }

    /**
     * Tests the built-in substring function
     */
    public function testBuiltInSubstringFunction()
    {
        $string = "foo";
        $this->template->setVar("string", $string);
        // Test with string and start parameters
        $this->template->readFromInput('{{substr($string, 1)}}');
        $this->assertEquals(substr($string, 1), $this->template->render());
        // Test with string, start, and length parameters
        $this->template->readFromInput('{{substr($string, 0, -1)}}');
        $this->assertEquals(substr($string, 0, -1), $this->template->render());
    }

    /**
     * Tests the built-in trim function
     */
    public function testBuiltInTrimFunction()
    {
        $this->template->setVar("string", "foo ");
        $this->template->readFromInput('{{trim($string)}}');
        // Test with string parameter
        $this->assertEquals(trim("foo "), $this->template->render());
        // Test with string and character mask parameters
        $this->template->setVar("string", "foo,");
        $this->template->readFromInput('{{trim($string, ",")}}');
        $this->assertEquals(trim("foo,", ","), $this->template->render());
    }

    /**
     * Tests the built-in uppercase first function
     */
    public function testBuiltInUCFirstFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->readFromInput('{{ucfirst($string)}}');
        $this->assertEquals(ucfirst("foo bar"), $this->template->render());
    }

    /**
     * Tests the built-in uppercase words function
     */
    public function testBuiltInUCWordsFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->readFromInput('{{ucwords($string)}}');
        $this->assertEquals(ucwords("foo bar"), $this->template->render());
    }

    /**
     * Tests the built-in URL decode function
     */
    public function testBuiltInURLDecodeFunction()
    {
        $this->template->setVar("string", "foo%27bar");
        $this->template->readFromInput('{{urldecode($string)}}');
        $this->assertEquals(urldecode("foo%27bar"), $this->template->render());
    }

    /**
     * Tests the built-in URL encode function
     */
    public function testBuiltInURLEncodeFunction()
    {
        $this->template->setVar("string", "foo/bar");
        $this->template->readFromInput('{{urlencode($string)}}');
        $this->assertEquals(urlencode("foo/bar"), $this->template->render());
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
        $this->template->registerFunction("customDate", function (\DateTime $date, $format, array $someArray)
        {
            return $date->format($format) . " and count of array is " . count($someArray);
        });
        $today = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->template->setVar("today", $today);

        return $today->format("m/d/Y") . " and count of array is 3";
    }
} 