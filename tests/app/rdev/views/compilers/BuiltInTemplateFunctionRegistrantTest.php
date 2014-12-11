<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the built-in template function registrant
 */
namespace RDev\Views\Compilers;
use RDev\Files;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Filters;

class BuiltInTemplateFunctionRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var Views\Template The template to use in the tests */
    private $template = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $xssFilter = new Filters\XSS();
        $fileSystem = new Files\FileSystem();
        $cache = new Cache\Cache($fileSystem, __DIR__ . "/tmp");
        $this->compiler = new Compiler($cache, $xssFilter);
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
     * Tests the built-in CSS function
     */
    public function testBuiltInCSSFunction()
    {
        // Test a single value
        $this->template->setContents('{{!css("foo")!}}');
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">',
            $this->compiler->compile($this->template)
        );

        // Test multiple values
        $this->template->setContents('{{!css(["foo", "bar"])!}}');
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">' .
            "\n" .
            '<link href="bar" rel="stylesheet">',
            $this->compiler->compile($this->template)
        );
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
     * Tests the built-in charset function
     */
    public function testBuiltInCharsetFunction()
    {
        $charset = "utf-8";
        $this->template->setContents('{{!charset("' . $charset . '")!}}');
        $this->assertEquals(
            '<meta charset="' . $charset . '">',
            $this->compiler->compile($this->template)
        );
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
     * Tests the built-in favicon function
     */
    public function testBuiltInFaviconFunction()
    {
        $path = "foo";
        $this->template->setContents('{{!favicon("' . $path . '")!}}');
        $this->assertEquals(
            '<link href="' . $path . '" rel="shortcut icon">',
            $this->compiler->compile($this->template)
        );
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
     * Tests the built-in http-equiv function
     */
    public function testBuiltInHTTPEquivFunction()
    {
        $name = "refresh";
        $value = 30;
        $this->template->setContents('{{!httpEquiv("' . $name . '", ' . $value . ')!}}');
        $this->assertEquals(
            '<meta http-equiv="' . $name . '" content="' . $value . '">',
            $this->compiler->compile($this->template)
        );
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
     * Tests the built-in meta description function
     */
    public function testBuiltInMetaDescriptionFunction()
    {
        $metaDescription = "A&W is a root beer";
        $this->template->setContents('{{!metaDescription("' . $metaDescription . '")!}}');
        $this->assertEquals(
            '<meta name="description" content="' . htmlentities($metaDescription) . '">',
            $this->compiler->compile($this->template)
        );
    }

    /**
     * Tests the built-in meta keywords function
     */
    public function testBuiltInMetaKeywordsFunction()
    {
        $metaKeywords = ["A&W", "root beer"];
        $this->template->setContents('{{!metaKeywords(["' . implode('","', $metaKeywords) . '"])!}}');
        $this->assertEquals(
            '<meta name="keywords" content="' . implode(",", array_map("htmlentities", $metaKeywords)) . '">',
            $this->compiler->compile($this->template)
        );
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
     * Tests the built-in script function
     */
    public function testBuiltInScriptFunction()
    {
        // Test a single value
        $this->template->setContents('{{!script("foo")!}}');
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>',
            $this->compiler->compile($this->template)
        );

        // Test multiple values
        $this->template->setContents('{{!script(["foo", "bar"])!}}');
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>' .
            "\n" .
            '<script type="text/javascript" src="bar"></script>',
            $this->compiler->compile($this->template)
        );

        // Test a single value with a type
        $this->template->setContents('{{!script("foo", "text/ecmascript")!}}');
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>',
            $this->compiler->compile($this->template)
        );

        // Test multiple values with a type
        $this->template->setContents('{{!script(["foo", "bar"], "text/ecmascript")!}}');
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>' .
            "\n" .
            '<script type="text/ecmascript" src="bar"></script>',
            $this->compiler->compile($this->template)
        );
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
     * Tests the built-in HTML title function
     */
    public function testBuiltInTitleFunction()
    {
        $title = "A&W";
        $this->template->setContents('{{!pageTitle("' . $title . '")!}}');
        $this->assertEquals('<title>' . htmlentities($title) . '</title>', $this->compiler->compile($this->template));
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
}