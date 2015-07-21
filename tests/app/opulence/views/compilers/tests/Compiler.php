<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the base class for compiler tests
 */
namespace Opulence\Tests\Views\Compilers\Tests;
use DateTime;
use Opulence\Files\FileSystem;
use Opulence\Views\Caching\Cache;
use Opulence\Views\Compilers\Compiler as ViewCompiler;
use Opulence\Views\Factories\ViewFactory;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\FortuneView;

abstract class Compiler extends \PHPUnit_Framework_TestCase
{
    /** The path to the test view with default tags */
    const VIEW_PATH_WITH_DEFAULT_TAG_DELIMITERS = "/../files/TestWithDefaultTagDelimiters.html";
    /** The path to the test view with custom tags */
    const VIEW_PATH_WITH_CUSTOM_TAG_DELIMITERS = "/../files/TestWithCustomTagDelimiters.html";
    /** The path to the test view with PHP code */
    const VIEW_PATH_WITH_PHP_CODE = "/../files/TestWithPHP.html";
    /** The path to the test view with a part defined */
    const VIEW_PATH_WITH_PART_DEFINED = "/../files/MasterWithPartDefined.html";
    /** The path to the test view with a part undefined */
    const VIEW_PATH_WITH_PART_UNDEFINED = "/../files/MasterWithPartUndefined.html";
    /** The path to the test view that calls its parent's part */
    const VIEW_PATH_THAT_CALLS_PARENT_PART = "/../files/ChildWithDefinedParentStatement.html";
    /** The path to the test view that calls its parent's undefined part */
    const VIEW_PATH_THAT_CALLS_PARENT_UNDEFINED_PART = "/../files/ChildWithUndefinedParentStatement.html";
    /** The path to the test view that just extends another */
    const VIEW_PATH_THAT_ONLY_EXTENDS_ANOTHER = "/../files/EmptyChild.html";
    /** The path to the test view with PHP code */
    const VIEW_PATH_WITH_INVALID_PHP_CODE = "/../files/TestWithInvalidPHP.html";
    /** the path to the test view with an extend statement */
    const VIEW_PATH_WITH_EXTEND_STATEMENT = "/../files/TestWithExtendStatement.html";
    /** the path to the test view with extend and part statements */
    const VIEW_PATH_WITH_EXTEND_AND_PART_STATEMENT = "/../files/TestWithExtendAndPartStatements.html";
    /** the path to the test view with nested extend statements */
    const VIEW_PATH_WITH_NESTED_EXTEND_STATEMENTS = "/../files/TestWithNestedExtendStatements.html";
    /** the path to the test view with "Foo" as content */
    const VIEW_PATH_WITH_FOO = "/../files/Foo.html";
    /** the path to the test view with an include statement */
    const VIEW_PATH_WITH_INCLUDE_STATEMENT = "/../files/TestWithIncludeStatement.html";
    /** the path to the test view with nested include statements */
    const VIEW_PATH_WITH_NESTED_INCLUDE_STATEMENTS = "/../files/TestWithNestedIncludeStatements.html";

    /** @var Cache The view cache */
    protected $cache = null;
    /** @var XSSFilter The cross-site scripting filter to use */
    protected $xssFilter = null;
    /** @var ViewFactory The view factory */
    protected $viewFactory = null;
    /** @var ViewCompiler $compiler The compiler to use in tests */
    protected $compiler = null;
    /** @var FortuneView The view to use in the tests */
    protected $view = null;
    /** @var FileSystem The file system used to read views */
    protected $fileSystem = null;

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
        $this->markTestSkipped();
        /*$this->xssFilter = new XSSFilter();
        $this->fileSystem = new FileSystem();
        $this->cache = new Cache($this->fileSystem, __DIR__ . "/tmp");
        $this->viewFactory = new ViewFactory($this->fileSystem, __DIR__ . "/../../files");
        $this->compiler = new ViewCompiler($this->cache, $this->viewFactory, $this->xssFilter);
        $this->view = new View();*/
    }

    /**
     * Registers a function to the view for use in testing
     *
     * @return string The expected result of the compiler
     */
    protected function registerFunction()
    {
        $this->compiler->registerViewFunction("customDate", function (DateTime $date, $format, array $someArray)
        {
            return $date->format($format) . " and count of array is " . count($someArray);
        });
        $today = new DateTime("now");
        $this->view->setVar("today", $today);

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
    protected function stringsWithEncodedCharactersEqual($string1, $string2)
    {
        // Convert ampersand
        $string1 = str_replace("&#38;", "&amp;", $string1);
        $string2 = str_replace("&#38;", "&amp;", $string2);
        // Convert single quote
        $string1 = str_replace("&#039", "&#39", $string1);
        $string2 = str_replace("&#039", "&#39", $string2);
        // Convert double quotes
        $string1 = str_replace("&quot;", "&#34;", $string1);
        $string2 = str_replace("&quot;", "&#34;", $string2);

        if($string1 === $string2)
        {
            return true;
        }
        else
        {
            error_log($string1 . "::" . $string2);

            return false;
        }
    }
}