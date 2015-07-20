<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Fortune template function registrant
 */
namespace Opulence\Views\Compilers\SubCompilers\Fortune;
use Opulence\Views\Compilers\Lexers\Lexer;
use Opulence\Views\Compilers\Parsers\Parser;
use Opulence\Views\Filters\XSSFilter;

class FortuneTemplateFunctionRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var FortuneCompiler The compiler to use in tests */
    private $compiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $xssFilter = new XSSFilter();
        $this->compiler = new FortuneCompiler(new Lexer(), new Parser(), $xssFilter);
    }

    /**
     * Tests the built-in CSS function
     */
    public function testBuiltInCSSFunction()
    {
        // Test a single value
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">',
            $this->compiler->callTemplateFunction("css", "foo")
        );

        // Test multiple values
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">' .
            "\n" .
            '<link href="bar" rel="stylesheet">',
            $this->compiler->callTemplateFunction("css", ["foo", "bar"])
        );
    }

    /**
     * Tests the built-in charset function
     */
    public function testBuiltInCharsetFunction()
    {
        $charset = "utf-8";
        $this->assertEquals(
            '<meta charset="' . $charset . '">',
            $this->compiler->callTemplateFunction("charset", $charset)
        );
    }

    /**
     * Tests the built-in favicon function
     */
    public function testBuiltInFaviconFunction()
    {
        $path = "foo";
        $this->assertEquals(
            '<link href="' . $path . '" rel="shortcut icon">',
            $this->compiler->callTemplateFunction("favicon", $path)
        );
    }

    /**
     * Tests the built-in http-equiv function
     */
    public function testBuiltInHTTPEquivFunction()
    {
        $name = "refresh";
        $value = 30;
        $this->assertEquals(
            '<meta http-equiv="' . $name . '" content="' . $value . '">',
            $this->compiler->callTemplateFunction("httpEquiv", $name, $value)
        );
    }

    /**
     * Tests the built-in meta description function
     */
    public function testBuiltInMetaDescriptionFunction()
    {
        $metaDescription = "A&W is a root beer";
        $this->assertEquals(
            '<meta name="description" content="' . htmlentities($metaDescription) . '">',
            $this->compiler->callTemplateFunction("metaDescription", $metaDescription)
        );
    }

    /**
     * Tests the built-in meta keywords function
     */
    public function testBuiltInMetaKeywordsFunction()
    {
        $metaKeywords = ["A&W", "root beer"];
        $this->assertEquals(
            '<meta name="keywords" content="' . implode(",", array_map("htmlentities", $metaKeywords)) . '">',
            $this->compiler->callTemplateFunction("metaKeywords", $metaKeywords)
        );
    }

    /**
     * Tests the built-in script function
     */
    public function testBuiltInScriptFunction()
    {
        // Test a single value
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>',
            $this->compiler->callTemplateFunction("script", "foo")
        );

        // Test multiple values
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>' .
            PHP_EOL .
            '<script type="text/javascript" src="bar"></script>',
            $this->compiler->callTemplateFunction("script", ["foo", "bar"])
        );

        // Test a single value with a type
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>',
            $this->compiler->callTemplateFunction("script", "foo", "text/ecmascript")
        );

        // Test multiple values with a type
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>' .
            "\n" .
            '<script type="text/ecmascript" src="bar"></script>',
            $this->compiler->callTemplateFunction("script", ["foo", "bar"], "text/ecmascript")
        );
    }

    /**
     * Tests the built-in HTML title function
     */
    public function testBuiltInTitleFunction()
    {
        $title = "A&W";
        $this->assertEquals(
            '<title>' . htmlentities($title) . '</title>',
            $this->compiler->callTemplateFunction("pageTitle", $title)
        );
    }
}