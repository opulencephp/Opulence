<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the template compiler
 */
namespace RDev\Views\Compilers;
use RDev\Files;
use RDev\Tests\Mocks;
use RDev\Views;
use RDev\Views\Cache;

class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tags */
    const TEMPLATE_PATH_WITH_DEFAULT_TAGS = "/../files/TestWithDefaultTags.html";
    /** The path to the test template with custom tags */
    const TEMPLATE_PATH_WITH_CUSTOM_TAGS = "/../files/TestWithCustomTags.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_PHP_CODE = "/../files/TestWithPHP.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_INVALID_PHP_CODE = "/../files/TestWithInvalidPHP.html";

    /** @var Compiler $compiler The compiler to use in tests */
    private $compiler = null;
    /** @var Views\Template The template to use in the tests */
    private $template = null;
    /** @var Files\FileSystem The file system used to read templates */
    private $fileSystem = null;

    /**
     * Does some setup before any tests
     */
    public static function setUpBeforeClass()
    {
        if(!is_dir(__DIR__ . "/tmp"))
        {
            mkdir(__DIR__ . "/tmp");
        }
    }

    /**
     * Performs some garbage collection
     */
    public static function tearDownAfterClass()
    {
        $files = glob(__DIR__ . "/tmp/*");

        foreach($files as $file)
        {
            is_dir($file) ? rmdir($file) : unlink($file);
        }

        rmdir(__DIR__ . "/tmp");
    }

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->fileSystem = new Files\FileSystem();
        $cache = new Cache\Cache($this->fileSystem, __DIR__ . "/tmp");
        $this->compiler = new Compiler($cache);
        $this->template = new Views\Template();
    }

    /**
     * Tests the built-in absolute value function
     */
    public function testBuiltInAbsFunction()
    {
        $number = -3.9;
        $this->template->setVar("number", $number);
        $this->template->setContents('{{!abs($number)!}}');
        $this->assertEquals(abs($number), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in ceiling function
     */
    public function testBuiltInCeilFunction()
    {
        $number = 3.9;
        $this->template->setVar("number", $number);
        $this->template->setContents('{{!ceil($number)!}}');
        $this->assertEquals(ceil($number), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in count function
     */
    public function testBuiltInCountFunction()
    {
        $array = [1, 2, 3];
        $this->template->setVar("array", $array);
        $this->template->setContents('{{!count($array)!}}');
        $this->assertEquals(count($array), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in date function
     */
    public function testBuiltInDateFunction()
    {
        // For the purposes of this test, we need to set a default timezone
        date_default_timezone_set("UTC");
        $format = "Ymd";
        $now = time();
        $this->template->setVar("format", $format);
        $this->template->setContents('{{!date($format)!}}');
        $this->assertEquals(date($format), $this->compiler->compile($this->template));
        $this->template->setVar("now", $now);
        $this->template->setContents('{{!date($format, $now)!}}');
        $this->assertEquals(date($format, $now), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in DateTime format function
     */
    public function testBuiltInDateTimeFormatFunction()
    {
        $today = new \DateTime("now");
        $this->template->setVar("today", $today);
        $this->template->setContents('{{!formatDateTime($today)!}}');
        $this->template->setVar("today", $today);
        // Test with date parameter
        $this->assertSame($today->format("m/d/Y"), $this->compiler->compile($this->template));
        // Test with date and format parameters
        $format = "Y-m-d";
        $this->template->setContents('{{!formatDateTime($today, "' . $format . '")!}}');
        $this->assertSame($today->format($format), $this->compiler->compile($this->template));
        // Test with date, format, and DateTimeZone timezone parameters
        $format = "Y-m-d";
        $timeZoneIdentifier = "America/New_York";
        $timezone = new \DateTimeZone($timeZoneIdentifier);
        $today->setTimezone($timezone);
        $this->template->setVar("timezone", $timezone);
        $this->template->setContents('{{!formatDateTime($today, "' . $format . '", $timezone)!}}');
        $this->assertSame($today->format($format), $this->compiler->compile($this->template));
        // Test with date, format, and string timezone parameters
        $this->template->setVar("timezone", $timeZoneIdentifier);
        $this->template->setContents('{{!formatDateTime($today, "' . $format . '", $timezone)!}}');
        $this->assertSame($today->format($format), $this->compiler->compile($this->template));
        // Test an invalid timezone
        $this->template->setVar("timezone", []);
        $this->template->setContents('{{!formatDateTime($today, "' . $format . '", $timezone)!}}');
        $this->assertSame($today->format($format), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in floor function
     */
    public function testBuiltInFloorFunction()
    {
        $number = 3.9;
        $this->template->setVar("number", $number);
        $this->template->setContents('{{!floor($number)!}}');
        $this->assertEquals(floor($number), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in implode function
     */
    public function testBuiltInImplodeFunction()
    {
        $array = [1, 2, 3];
        $this->template->setVar("array", $array);
        $this->template->setContents('{{!implode(",", $array)!}}');
        $this->assertEquals(implode(",", $array), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in JSON encode function
     */
    public function testBuiltInJSONEncodeFunction()
    {
        $array = ["foo" => ["bar" => "blah"]];
        $this->template->setVar("array", $array);
        // Test with value parameter
        $this->template->setContents('{{!json_encode($array)!}}');
        $this->assertEquals(json_encode($array), $this->compiler->compile($this->template));
        // Test with value and options parameters
        $this->template->setVar("options", JSON_HEX_TAG);
        $this->template->setContents('{{!json_encode($array, $options)!}}');
        $this->assertEquals(json_encode($array, JSON_HEX_TAG), $this->compiler->compile($this->template));
        // Test with value, options, and depth parameters
        $this->template->setVar("depth", 1);
        $this->template->setContents('{{!json_encode($array, $options, $depth)!}}');
        $this->assertEquals(json_encode($array, JSON_HEX_TAG, 1), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in lowercase first function
     */
    public function testBuiltInLCFirstFunction()
    {
        $this->template->setVar("string", "FOO BAR");
        $this->template->setContents('{{!lcfirst($string)!}}');
        $this->assertEquals(lcfirst("FOO BAR"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in round function
     */
    public function testBuiltInRoundFunction()
    {
        $number = 3.85;
        $this->template->setVar("number", $number);
        // Test with number parameter
        $this->template->setContents('{{!round($number)!}}');
        $this->assertEquals(round($number), $this->compiler->compile($this->template));
        // Test with number and precision parameters
        $this->template->setContents('{{!round($number, 1)!}}');
        $this->assertEquals(round($number, 1), $this->compiler->compile($this->template));
        // Test with number, precision, and mode parameters
        $this->template->setContents('{{!round($number, 0, PHP_ROUND_HALF_DOWN)!}}');
        $this->assertEquals(round($number, 0, PHP_ROUND_HALF_DOWN), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in lowercase function
     */
    public function testBuiltInStrToLowerFunction()
    {
        $this->template->setVar("string", "FOO BAR");
        $this->template->setContents('{{!strtolower($string)!}}');
        $this->assertEquals(strtolower("FOO BAR"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in uppercase function
     */
    public function testBuiltInStrToUpperFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->setContents('{{!strtoupper($string)!}}');
        $this->assertEquals(strtoupper("foo bar"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in substring function
     */
    public function testBuiltInSubstringFunction()
    {
        $string = "foo";
        $this->template->setVar("string", $string);
        // Test with string and start parameters
        $this->template->setContents('{{!substr($string, 1)!}}');
        $this->assertEquals(substr($string, 1), $this->compiler->compile($this->template));
        // Test with string, start, and length parameters
        $this->template->setContents('{{!substr($string, 0, -1)!}}');
        $this->assertEquals(substr($string, 0, -1), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in trim function
     */
    public function testBuiltInTrimFunction()
    {
        $this->template->setVar("string", "foo ");
        $this->template->setContents('{{!trim($string)!}}');
        // Test with string parameter
        $this->assertEquals(trim("foo "), $this->compiler->compile($this->template));
        // Test with string and character mask parameters
        $this->template->setVar("string", "foo,");
        $this->template->setContents('{{!trim($string, ",")!}}');
        $this->assertEquals(trim("foo,", ","), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in uppercase first function
     */
    public function testBuiltInUCFirstFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->setContents('{{!ucfirst($string)!}}');
        $this->assertEquals(ucfirst("foo bar"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in uppercase words function
     */
    public function testBuiltInUCWordsFunction()
    {
        $this->template->setVar("string", "foo bar");
        $this->template->setContents('{{!ucwords($string)!}}');
        $this->assertEquals(ucwords("foo bar"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in URL decode function
     */
    public function testBuiltInURLDecodeFunction()
    {
        $this->template->setVar("string", "foo%27bar");
        $this->template->setContents('{{!urldecode($string)!}}');
        $this->assertEquals(urldecode("foo%27bar"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the built-in URL encode function
     */
    public function testBuiltInURLEncodeFunction()
    {
        $this->template->setVar("string", "foo/bar");
        $this->template->setContents('{{!urlencode($string)!}}');
        $this->assertEquals(urlencode("foo/bar"), $this->compiler->compile($this->template));
    }

    /**
     * Tests the compiler priorities
     */
    public function testCompilerPriority()
    {
        $template = new Views\Template();
        $template->setContents("");
        // Although this one is registered first, it doesn't have priority
        $this->compiler->registerCompiler(function ($template, $content)
        {
            return $content . "3";
        });
        // This one has the second highest priority, so it should be compiled second
        $this->compiler->registerCompiler(function ($template, $content)
        {
            return $content . "2";
        }, 2);
        // This one has the highest priority, so it should be compiled first
        $this->compiler->registerCompiler(function ($template, $content)
        {
            return $content . "1";
        }, 1);
        $this->assertEquals("123", $this->compiler->compile($template));
    }

    /**
     * Tests compiling invalid PHP
     */
    public function testCompilingInvalidPHP()
    {
        $this->setExpectedException("\\RuntimeException");
        $contents = $this->fileSystem->read(__DIR__ . "/../files/TestWithInvalidPHP.html");
        $this->template->setContents($contents);
        $this->compiler->compile($this->template);
    }

    /**
     * Tests compiling a template that uses custom tags
     */
    public function testCompilingTemplateWithCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_TAGS);
        $this->template->setContents($contents);
        $this->template->setUnescapedOpenTag("^^");
        $this->template->setUnescapedCloseTag("$$");
        $this->template->setEscapedOpenTag("++");
        $this->template->setEscapedCloseTag("--");
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template that uses the default tags
     */
    public function testCompilingTemplateWithDefaultTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAGS);
        $this->template->setContents($contents);
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template with PHP code
     */
    public function testCompilingTemplateWithPHPCode()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_PHP_CODE);
        $this->template->setContents($contents);
        $user1 = new Mocks\User(1, "foo");
        $user2 = new Mocks\User(2, "bar");
        $this->template->setTag("listDescription", "usernames");
        $this->template->setVar("users", [$user1, $user2]);
        $this->template->setVar("coolestGuy", "Dave");
        $functionResult = $this->registerFunction();
        $this->assertEquals('TEST List of usernames on ' . $functionResult . ':
<ul>
    <li>foo</li><li>bar</li></ul> 2 items
<br>Dave is a pretty cool guy. Alternative syntax works! I agree. Fake closing PHP tag: ?>', $this->compiler->compile($this->template));
    }

    /**
     * Tests compiling a template whose custom tags we didn't set
     */
    public function testCompilingTemplateWithUnsetCustomTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_CUSTOM_TAGS);
        $this->template->setContents($contents);
        $this->template->setUnescapedOpenTag("^^");
        $this->template->setUnescapedCloseTag("$$");
        $this->template->setEscapedOpenTag("++");
        $this->template->setEscapedCloseTag("--");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! ^^blah$$. . me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template whose tags we didn't set
     */
    public function testCompilingTemplateWithUnsetTags()
    {
        $contents = $this->fileSystem->read(__DIR__ . self::TEMPLATE_PATH_WITH_DEFAULT_TAGS);
        $this->template->setContents($contents);
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! {{!blah!}}. . me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult.",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a template with a function that has spaces between the open and close tags
     */
    public function testFunctionWithSpacesBetweenTags()
    {
        $this->template->setContents('{{! foo("bar") !}}');
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            echo $input;
        });
        $this->assertEquals("bar", $this->compiler->compile($this->template));
    }

    /**
     * Tests passing in an integer less than 1 for the priority
     */
    public function testIntegerLessThanOnePriority()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->registerCompiler(function ($template, $content)
        {
            return $content;
        }, 0);
    }

    /**
     * Tests compiling a template with multiple calls to the same function
     */
    public function testMultipleCallsOfSameFunction()
    {
        $this->compiler->registerTemplateFunction("foo", function ($param1 = null, $param2 = null)
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
        $this->template->setContents(
            '{{!foo()!}}, {{!foo()!}}, {{!foo("bar")!}}, {{!foo(null, "bar")!}}, {{!foo("bar", "blah")!}}'
        );
        $this->assertEquals(
            'No params, No params, Param 1 set, Param 2 set, Both params set',
            $this->compiler->compile($this->template)
        );
    }

    /**
     * Tests passing in a non-integer for the priority
     */
    public function testNonIntegerPriority()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->registerCompiler(function ($template, $content)
        {
            return $content;
        }, 1.5);
    }

    /**
     * Tests registering an invalid compiler
     */
    public function testRegisteringInvalidCompiler()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->registerCompiler([]);
    }

    /**
     * Registers a function to the template for use in testing
     *
     * @return string The expected result of the compiler
     */
    private function registerFunction()
    {
        $this->compiler->registerTemplateFunction("customDate", function (\DateTime $date, $format, array $someArray)
        {
            return $date->format($format) . " and count of array is " . count($someArray);
        });
        $today = new \DateTime("now");
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