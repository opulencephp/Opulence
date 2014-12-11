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
use RDev\Views\Filters;

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

    /** @var Filters\IFilter The cross-site scripting filter to use */
    private $xssFilter = null;
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
        $this->xssFilter = new Filters\XSS();
        $this->fileSystem = new Files\FileSystem();
        $cache = new Cache\Cache($this->fileSystem, __DIR__ . "/tmp");
        $this->compiler = new Compiler($cache, $this->xssFilter);
        $this->template = new Views\Template();
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
     * Tests compiling a function inside escaped tags
     */
    public function testCompilingFunctionInsideEscapedTags()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "A&W";
        });
        $this->template->setContents("{{foo()}}");
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "A&amp;W",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests compiling a function inside unescaped tags
     */
    public function testCompilingFunctionInsideUnescapedTags()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "A&W";
        });
        $this->template->setContents("{{!foo()!}}");
        $this->assertEquals("A&W", $this->compiler->compile($this->template));
    }

    /**
     * Tests compiling invalid PHP
     */
    public function testCompilingInvalidPHP()
    {
        $this->setExpectedException("\\RuntimeException");
        $contents = $this->fileSystem->read(__DIR__ . "/../files/TestWithInvalidPHP.html");
        $this->template->setContents($contents);
        // Temporarily disable error reporting to prevent stuff from being printed in the error logs
        $originalErrorReporting = error_reporting();
        error_reporting(0);
        $this->compiler->compile($this->template);
        error_reporting($originalErrorReporting);
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
        $this->template->setStatementOpenTag("(*");
        $this->template->setStatementCloseTag("*)");
        $this->template->setTag("foo", "Hello");
        $this->template->setTag("bar", "world");
        $this->template->setTag("imSafe", "a&b");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                "Hello, world! It worked. ^^blah$$. a&amp;b. me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult. .",
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
                "Hello, world! It worked. {{!blah!}}. a&amp;b. me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult. .",
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
        $this->template->setStatementOpenTag("(*");
        $this->template->setStatementCloseTag("*)");
        $functionResult = $this->registerFunction();
        $this->assertTrue(
            $this->stringsWithEncodedCharactersEqual(
                ", ! It worked. ^^blah$$. . me too. c&amp;d. e&f. ++\"g&h\"--. ++ \"i&j\" --. ++blah--. Today escaped is $functionResult and unescaped is $functionResult. .",
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
                ", ! It worked. {{!blah!}}. . me too. c&amp;d. e&f. {{\"g&h\"}}. {{ \"i&j\" }}. {{blah}}. Today escaped is $functionResult and unescaped is $functionResult. .",
                $this->compiler->compile($this->template)
            )
        );
    }

    /**
     * Tests escaping a part statement
     */
    public function testEscapingPartStatement()
    {
        $contents = '\{% part("foo") %}bar{% endpart %}';
        $this->template->setContents($contents);
        $this->assertEquals($contents, $this->compiler->compile($this->template));
    }

    /**
     * Tests executing a non-existent template function
     */
    public function testExecutingNonExistentTemplateFunction()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->compiler->executeTemplateFunction("nonExistentFunction");
    }

    /**
     * Tests executing a template function that takes no parameters
     */
    public function testExecutingTemplateFunctionThatTakesNoParameters()
    {
        $this->compiler->registerTemplateFunction("foo", function ()
        {
            return "bar";
        });
        $this->assertEquals("bar", $this->compiler->executeTemplateFunction("foo"));
    }

    /**
     * Tests executing a template function that takes parameters
     */
    public function testExecutingTemplateFunctionThatTakesParameters()
    {
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            return "foo" . $input;
        });
        $this->assertEquals("foobar", $this->compiler->executeTemplateFunction("foo", ["bar"]));
    }

    /**
     * Tests compiling a template with a function that spans multiple lines
     */
    public function testFunctionThatSpansMultipleLines()
    {
        $this->compiler->registerTemplateFunction("foo", function ($input)
        {
            return $input . "bar";
        });
        $this->template->setContents("{{
        foo(
        'foo'
        )
        }}");
        $this->assertEquals("foobar", $this->compiler->compile($this->template));
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
     * Tests getting the XSS filter
     */
    public function testGettingXSSFilter()
    {
        $this->assertSame($this->xssFilter, $this->compiler->getXSSFilter());
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
     * Tests the part statement with double quotes
     */
    public function testPartStatementWithDoubleQuotes()
    {
        $this->template->setContents('{{foo}} {% part("foo") %}bar{% endpart %}');
        $this->assertEquals("bar ", $this->compiler->compile($this->template));
    }

    /**
     * Tests the part statement with single quotes
     */
    public function testPartStatementWithSingleQuotes()
    {
        $this->template->setContents("{{foo}} {% part('foo') %}bar{% endpart %}");
        $this->assertEquals("bar ", $this->compiler->compile($this->template));
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
     * Tests setting the XSS filter
     */
    public function testSettingXSSFilter()
    {
        $xssFilter = new Filters\XSS();
        $this->compiler->setXSSFilter($xssFilter);
        $this->assertSame($xssFilter, $this->compiler->getXSSFilter());
    }

    /**
     * Tests compiling a template with a tag that spans multiple lines
     */
    public function testTagThatSpansMultipleLines()
    {
        $this->template->setContents("{{
        foo
        }}");
        $this->template->setTag("foo", "bar");
        $this->assertEquals("bar", $this->compiler->compile($this->template));
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