<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Fortune view function registrant
 */
namespace Opulence\Views\Compilers\Fortune;

use Opulence\Views\Caching\ICache;
use Opulence\Views\Filters\XSSFilter;
use Opulence\Views\Compilers\Fortune\Lexers\Lexer;
use Opulence\Views\Compilers\Fortune\Parsers\Parser;

class ViewFunctionRegistrantTest extends \PHPUnit_Framework_TestCase
{
    /** @var Transpiler The transpiler to use in tests */
    private $transpiler = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $xssFilter = new XSSFilter();
        /** @var ICache|\PHPUnit_Framework_MockObject_MockObject $cache */
        $cache = $this->getMock(ICache::class);
        $cache->expects($this->any())
            ->method("has")
            ->willReturn(false);
        $this->transpiler = new Transpiler(new Lexer(), new Parser(), $cache, $xssFilter);
    }

    /**
     * Tests the built-in CSS function
     */
    public function testBuiltInCSSFunction()
    {
        // Test a single value
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">',
            $this->transpiler->callViewFunction("css", "foo")
        );

        // Test multiple values
        $this->assertEquals(
            '<link href="foo" rel="stylesheet">' .
            "\n" .
            '<link href="bar" rel="stylesheet">',
            $this->transpiler->callViewFunction("css", ["foo", "bar"])
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
            $this->transpiler->callViewFunction("charset", $charset)
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
            $this->transpiler->callViewFunction("favicon", $path)
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
            $this->transpiler->callViewFunction("httpEquiv", $name, $value)
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
            $this->transpiler->callViewFunction("metaDescription", $metaDescription)
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
            $this->transpiler->callViewFunction("metaKeywords", $metaKeywords)
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
            $this->transpiler->callViewFunction("script", "foo")
        );

        // Test multiple values
        $this->assertEquals(
            '<script type="text/javascript" src="foo"></script>' .
            PHP_EOL .
            '<script type="text/javascript" src="bar"></script>',
            $this->transpiler->callViewFunction("script", ["foo", "bar"])
        );

        // Test a single value with a type
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>',
            $this->transpiler->callViewFunction("script", "foo", "text/ecmascript")
        );

        // Test multiple values with a type
        $this->assertEquals(
            '<script type="text/ecmascript" src="foo"></script>' .
            "\n" .
            '<script type="text/ecmascript" src="bar"></script>',
            $this->transpiler->callViewFunction("script", ["foo", "bar"], "text/ecmascript")
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
            $this->transpiler->callViewFunction("pageTitle", $title)
        );
    }
}