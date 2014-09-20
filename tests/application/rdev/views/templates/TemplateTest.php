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
    /** The path to the test template with default tags */
    const TEMPLATE_PATH_WITH_DEFAULT_TAGS = "/files/TestWithDefaultTags.html";
    /** The path to the test template with custom tags */
    const TEMPLATE_PATH_WITH_CUSTOM_TAGS = "/files/TestWithCustomTags.html";
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
        $this->template->readFromInput('{{!abs($number)!}}');
        $this->assertEquals(abs($number), $this->template->render());
    }

    /**
     * Tests the built-in ceiling function
     */
    public function testBuiltInCeilFunction()
    {
        $number = 3.9;
        $this->template->setVar("number", $number);
        $this->template->readFromInput('{{!ceil($number)!}}');
        $this->assertEquals(ceil($number), $this->template->render());
    }

    /**
     * Tests the built-in count function
     */
    public function testBuiltInCountFunction()
    {
        $array = [1, 2, 3];
        $this->template->setVar("array", $array);
        $this->template->readFromInput('{{!count($array)!}}');
        $this->assertEquals(count($array), $this->template->render());
    }

    /**
     * Tests the built-in date function
     */
    public function testBuiltInDateFunction()
    {
        $today = new \DateTime("now", new \DateTimeZone("UTC"));
        $this->template->setVar("today", $today);
        $this->template->readFromInput('{{!date($today)!}}');
        $this->template->setVar("today", $today);
        // Test with date parameter
        $this->assertSame($today->format("m/d/Y"), $this->template->render());
        // Test with date and format parameters
        $format = "Y-m-d";
        $this->template->readFromInput('{{!date($today, "' . $format . '")!}}');
        $this->assertSame($today->format($format), $this->template->render());
        // Test with date, format, and timezone parameters
        $format = "Y-m-d";
        $timezone = new \DateTimeZone("America/New_York");
        $today->setTimezone($timezone);
        $this->template->setVar("timezone", $timezone);
        $this->template->readFromInput('{{!date($today, "' . $format . '", $timezone)!}}');
        $this->assertSame($today->format($format), $this->template->render());
        // Test an invalid timezone
        $this->template->setVar("timezone", []);
        $this->template->readFromInput('{{!date($today, "' . $format . '", $timezone)!}}');
        $this->assertSame($today->format($format), $this->template->render());
    }

    /**
     * Tests the built-in floor function
     */
    public function testBuiltInFloorFunction()
    {
        $number = 3.9;
        $this->template->setVar("number", $number);
        $this->template->readFromInput('{{!floor($number)!}}');
        $this->assertEquals(floor($number), $this->template->render());
    }

    /**
     * Tests the built-in implode function
     */
    public function testBuiltInImplodeFunction()
    {
        $array = [1, 2, 3];
        $this->template->setVar("array", $array);
        $this->template->readFromInput('{{!implode(",", $array)!}}');
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
        $this->template->readFromInput('{{!json_encode($array)!}}');
        $this->assertEquals(json_encode($array), $this->template->render());
        // Test with value and options parameters
        $this->template->setVar("options", JSON_HEX_TAG);
        $this->template->readFromInput('{{!json_encode($array, $options)!}}');
        $this->assertEquals(json_encode($array, JSON_HEX_TAG), $this->template->render());
        // Test with value, options, and depth parameters
        $this->template->setVar("depth", 1);
        $this->template->readFromInput('{{!json_encode($array, $options, $depth)!}}');
        $this->assertEquals(json_encode($array, JSON_HEX_TAG, 1), $this->template->render());
    }

    /**
     * Tests the built-in lowercase first function
     */
    public function testBuiltInLCFirstFunction()
    {
        $this->template->setVar("string", "FOO BAR");
        $this->template->readFromInput('{{!lcfirst($string)!}}');
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
        $this->template->readFromInput('{{!round($number)!}}');
        $this->assertEquals(round($number), $this->template->render());
        // Test with number and precision parameters
        $this->template->readFromInput('{{!round($number, 1)!}}');
        $this->assertEquals(round($number, 1), $this->template->render());
        // Test with number, precision, and mode parameters
        $this->template->readFromInput('{{!round($number, 0, PHP_ROUND_HALF_DOWN)!}}');
        $this->assertEquals(round($number, 0, PHP_ROUND_HALF_DOWN), $this->template->render());
    }

    /**
     * Tests the built-in lowercase function
     */
    public function testBuiltInStrToLowerFunction()
    {
        $this->template->setVar("string", "FOO BAR");
        $this->template->readFromInput('{{!strtolower($string)!}}');
        $this->assertEquals(strtolower("FOO BAR"), $this->template->render());
    }

    /**
     * Tests the built-in uppercase function
     */
    public function testBuiltInStrToUpperFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->readFromInput('{{!strtoupper($string)!}}');
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
        $this->template->readFromInput('{{!substr($string, 1)!}}');
        $this->assertEquals(substr($string, 1), $this->template->render());
        // Test with string, start, and length parameters
        $this->template->readFromInput('{{!substr($string, 0, -1)!}}');
        $this->assertEquals(substr($string, 0, -1), $this->template->render());
    }

    /**
     * Tests the built-in trim function
     */
    public function testBuiltInTrimFunction()
    {
        $this->template->setVar("string", "foo ");
        $this->template->readFromInput('{{!trim($string)!}}');
        // Test with string parameter
        $this->assertEquals(trim("foo "), $this->template->render());
        // Test with string and character mask parameters
        $this->template->setVar("string", "foo,");
        $this->template->readFromInput('{{!trim($string, ",")!}}');
        $this->assertEquals(trim("foo,", ","), $this->template->render());
    }

    /**
     * Tests the built-in uppercase first function
     */
    public function testBuiltInUCFirstFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->readFromInput('{{!ucfirst($string)!}}');
        $this->assertEquals(ucfirst("foo bar"), $this->template->render());
    }

    /**
     * Tests the built-in uppercase words function
     */
    public function testBuiltInUCWordsFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->readFromInput('{{!ucwords($string)!}}');
        $this->assertEquals(ucwords("foo bar"), $this->template->render());
    }

    /**
     * Tests the built-in URL decode function
     */
    public function testBuiltInURLDecodeFunction()
    {
        $this->template->setVar("string", "foo%27bar");
        $this->template->readFromInput('{{!urldecode($string)!}}');
        $this->assertEquals(urldecode("foo%27bar"), $this->template->render());
    }

    /**
     * Tests the built-in URL encode function
     */
    public function testBuiltInURLEncodeFunction()
    {
        $this->template->setVar("string", "foo/bar");
        $this->template->readFromInput('{{!urlencode($string)!}}');
        $this->assertEquals(urlencode("foo/bar"), $this->template->render());
    }

    /**
     * Tests rendering a template with a function that has spaces between the open and close tags
     */
    public function testFunctionWithSpacesBetweenTags()
    {
        $this->template->readFromInput('{{! foo("bar") !}}');
        $this->template->registerFunction("foo", function ($input)
        {
            echo $input;
        });
        $this->assertEquals("bar", $this->template->render());
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
     * Tests getting the unrendered template from a file
     */
    public function testGettingUnrenderedTemplateFromAFile()
    {
        $templatePath = __DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAGS;
        $unrenderedTemplate = file_get_contents($templatePath);
        $this->template->readFromFile($templatePath);
        $this->assertEquals($unrenderedTemplate, $this->template->getUnrenderedTemplate());
    }

    /**
     * Tests getting the unrendered template from input
     */
    public function testGettingUnrenderedTemplateFromInput()
    {
        $unrenderedTemplate = "Hello, {{!username!}}";
        $this->template->readFromInput($unrenderedTemplate);
        $this->assertEquals($unrenderedTemplate, $this->template->getUnrenderedTemplate());
    }

    /**
     * Tests rendering a template with multiple calls to the same function
     */
    public function testMultipleCallsOfSameFunction()
    {
        $this->template->registerFunction("foo", function ($param1 = null, $param2 = null)
        {
            if($param1 == null && $param2 == null)
            {
                return "No params";
            }
            elseif($param1 == null)
            {
                return "Param 2 set";
            }
            elseif($param2 == null)
            {
                return "Param 1 set";
            }
            else
            {
                return "Both params set";
            }
        });
        $this->template->readFromInput('{{!foo()!}}, {{!foo()!}}, {{!foo("bar")!}}, {{!foo(null, "bar")!}}, {{!foo("bar", "blah")!}}');
        $this->assertEquals('No params, No params, Param 1 set, Param 2 set, Both params set', $this->template->render());
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
     * Tests rendering invalid PHP
     */
    public function testRenderingInvalidPHP()
    {
        $this->setExpectedException("\\RuntimeException");
        $this->template->readFromFile(__DIR__ . "/files/TestWithInvalidPHP.html");
        $this->template->render();
    }

    /**
     * Tests rendering a template that uses custom tags
     */
    public function testRenderingTemplateWithCustomTags()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_TAGS);
        $this->template->setUnescapedOpenTag("^^");
        $this->template->setUnescapedCloseTag("$$");
        $this->template->setEscapedOpenTag("++");
        $this->template->setEscapedCloseTag("--");
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
                "Hello, world! ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->template->render())
        );
    }

    /**
     * Tests rendering a template that uses the default tags
     */
    public function testRenderingTemplateWithDefaultTags()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAGS);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
                "Hello, world! {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->template->render())
        );
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
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_TAGS);
        $this->template->setUnescapedOpenTag("^^");
        $this->template->setUnescapedCloseTag("$$");
        $this->template->setEscapedOpenTag("++");
        $this->template->setEscapedCloseTag("--");
        $functionResult = $this->registerFunction();
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
                ", ! ^^blah$$. . me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->template->render())
        );
    }

    /**
     * Tests rendering a template whose tags we didn't set
     */
    public function testRenderingTemplateWithUnsetTags()
    {
        $this->template->readFromFile(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAGS);
        $functionResult = $this->registerFunction();
        $this->assertTrue($this->stringsWithEncodedCharactersEqual(
                ", ! {{!blah!}}. . me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->template->render())
        );
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

    /**
     * Checks if two strings with encoded characters are equal
     * This is necessary because, for example, HHVM encodes "&" to "&#38;" whereas PHP 5.6 encodes to "&amp;"
     * This method makes those two alternate characters equivalent
     *
     * @param string $string1 The first string to compare
     * @param string $string2 The second string to compare
     * @return bool True if the strings are equal, otherwise false
     */
    private function stringsWithEncodedCharactersEqual($string1, $string2)
    {
        $string1 = str_replace("&#38;", "&amp;", $string1);
        $string2 = str_replace("&#38;", "&amp;", $string2);

        return $string1 === $string2;
    }
} 