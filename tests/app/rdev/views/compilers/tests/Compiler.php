<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the base class for compiler tests
 */
namespace RDev\Tests\Views\Compilers\Tests;
use RDev\Files;
use RDev\Views;
use RDev\Views\Cache;
use RDev\Views\Compilers;
use RDev\Views\Factories;
use RDev\Views\Filters;

abstract class Compiler extends \PHPUnit_Framework_TestCase
{
    /** The path to the test template with default tags */
    const TEMPLATE_PATH_WITH_DEFAULT_TAG_DELIMITERS = "/../files/TestWithDefaultTagDelimiters.html";
    /** The path to the test template with custom tags */
    const TEMPLATE_PATH_WITH_CUSTOM_TAG_DELIMITERS = "/../files/TestWithCustomTagDelimiters.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_PHP_CODE = "/../files/TestWithPHP.html";
    /** The path to the test template with PHP code */
    const TEMPLATE_PATH_WITH_INVALID_PHP_CODE = "/../files/TestWithInvalidPHP.html";
    /** the path to the test template with an extend statement */
    const TEMPLATE_PATH_WITH_EXTEND_STATEMENT = "/../files/TestWithExtendStatement.html";
    /** the path to the test template with extend and part statements */
    const TEMPLATE_PATH_WITH_EXTEND_AND_PART_STATEMENT = "/../files/TestWithExtendAndPartStatements.html";
    /** the path to the test template with nested extend statements */
    const TEMPLATE_PATH_WITH_NESTED_EXTEND_STATEMENTS = "/../files/TestWithNestedExtendStatements.html";
    /** the path to the test template with "Foo" as content */
    const TEMPLATE_PATH_WITH_FOO = "/../files/Foo.html";
    /** the path to the test template with an include statement */
    const TEMPLATE_PATH_WITH_INCLUDE_STATEMENT = "/../files/TestWithIncludeStatement.html";
    /** the path to the test template with nested include statements */
    const TEMPLATE_PATH_WITH_NESTED_INCLUDE_STATEMENTS = "/../files/TestWithNestedIncludeStatements.html";

    /** @var Cache\Cache The view cache */
    protected $cache = null;
    /** @var Filters\IFilter The cross-site scripting filter to use */
    protected $xssFilter = null;
    /** @var Factories\ITemplateFactory The template factory */
    protected $templateFactory = null;
    /** @var Compilers\Compiler $compiler The compiler to use in tests */
    protected $compiler = null;
    /** @var Views\Template The template to use in the tests */
    protected $template = null;
    /** @var Files\FileSystem The file system used to read templates */
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
        $this->xssFilter = new Filters\XSS();
        $this->fileSystem = new Files\FileSystem();
        $this->cache = new Cache\Cache($this->fileSystem, __DIR__ . "/tmp");
        $this->templateFactory = new Factories\TemplateFactory($this->fileSystem, __DIR__ . "/../../files");
        $this->compiler = new Compilers\Compiler($this->cache, $this->templateFactory, $this->xssFilter);
        $this->template = new Views\Template();
    }

    /**
     * Registers a function to the template for use in testing
     *
     * @return string The expected result of the compiler
     */
    protected function registerFunction()
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
    protected function stringsWithEncodedCharactersEqual($string1, $string2)
    {
        // Convert ampersand
        $string1 = str_replace("&#38;", "&amp;", $string1);
        $string2 = str_replace("&#38;", "&amp;", $string2);
        // Convert single quote
        $string1 = str_replace("&#039", "&#39", $string1);
        $string2 = str_replace("&#039", "&#39", $string2);

        return $string1 === $string2;
    }
}