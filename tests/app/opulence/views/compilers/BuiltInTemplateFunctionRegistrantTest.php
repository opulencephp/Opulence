<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the built-in template function registrant
 */
namespace Opulence\Views\Compilers;
use Opulence\Files\FileSystem;
use Opulence\Views\Caching\Cache;
use Opulence\Views\Factories\TemplateFactory;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Template;

class BuiltInTemplateFunctionRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var Compiler The compiler to use in tests */
    private $compiler = null;
    /** @var Template The template to use in the tests */
    private $template = null;

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
        $xssFilter = new XSSFilter();
        $fileSystem = new FileSystem();
        $cache = new Cache($fileSystem, __DIR__ . "/tmp");
        $this->compiler = new Compiler($cache, new TemplateFactory($fileSystem, __DIR__), $xssFilter);
        $this->template = new Template();
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
     * Tests the built-in HTML title function
     */
    public function testBuiltInTitleFunction()
    {
        $title = "A&W";
        $this->template->setContents('{{!pageTitle("' . $title . '")!}}');
        $this->assertEquals('<title>' . htmlentities($title) . '</title>', $this->compiler->compile($this->template));
    }
}