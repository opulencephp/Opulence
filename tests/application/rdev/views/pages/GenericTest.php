<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the generic page template
 */
namespace RDev\Views\Pages;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /** @var Generic The generic to use in tests */
    private $generic = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->generic = new Generic();
    }

    /**
     * Tests adding inline CSS to the footer
     */
    public function testAddingFooterInlineCSS()
    {
        $css = "body{font-size: 13px;}";
        $this->generic->addFooterInlineCSS($css);
        $this->assertEquals([$css], $this->generic->getFooterInlineCSS());
    }

    /**
     * Tests adding inline JavaScript to the footer
     */
    public function testAddingFooterInlineJavaScript()
    {
        $javaScript = "alert('hi');";
        $this->generic->addFooterInlineJavaScript($javaScript);
        $this->assertEquals([$javaScript], $this->generic->getFooterInlineJavaScript());
    }

    /**
     * Tests adding multiple inline CSS to the header
     */
    public function testAddingHeaderInlineCSS()
    {
        $css = "body{font-size: 13px;}";
        $this->generic->addHeaderInlineCSS($css);
        $this->assertEquals([$css], $this->generic->getHeaderInlineCSS());
    }

    /**
     * Tests adding inline JavaScript to the header
     */
    public function testAddingHeaderInlineJavaScript()
    {
        $javaScript = "alert('hi');";
        $this->generic->addHeaderInlineJavaScript($javaScript);
        $this->assertEquals([$javaScript], $this->generic->getHeaderInlineJavaScript());
    }

    /**
     * Tests adding multiple CSS file paths to the footer
     */
    public function testAddingMultipleFooterCSSPaths()
    {
        $filePaths = ["foo/bar.css", "abc/xyz.css"];
        $this->generic->addFooterCSSFilePaths($filePaths);
        $this->assertEquals($filePaths, $this->generic->getFooterCSSFilePaths());
    }

    /**
     * Tests adding multiple inline CSS to the footer
     */
    public function testAddingMultipleFooterInlineCSS()
    {
        $cssList = ["body{font-size: 13px;}", "img{border: none;}"];

        foreach($cssList as $css)
        {
            $this->generic->addFooterInlineCSS($css);
        }

        $this->assertEquals($cssList, $this->generic->getFooterInlineCSS());
    }

    /**
     * Tests adding multiple inline JavaScript to the footer
     */
    public function testAddingMultipleFooterInlineJavaScript()
    {
        $javaScriptList = ["alert('hi');", "alert('foo');"];

        foreach($javaScriptList as $javaScript)
        {
            $this->generic->addFooterInlineJavaScript($javaScript);
        }

        $this->assertEquals($javaScriptList, $this->generic->getFooterInlineJavaScript());
    }

    /**
     * Tests adding multiple JavaScript file paths to the footer
     */
    public function testAddingMultipleFooterJavaScriptPaths()
    {
        $filePaths = ["foo/bar.js", "abc/xyz.js"];
        $this->generic->addFooterJavaScriptFilePaths($filePaths);
        $this->assertEquals($filePaths, $this->generic->getFooterJavaScriptFilePaths());
    }

    /**
     * Tests adding multiple CSS file paths to the header
     */
    public function testAddingMultipleHeaderCSSPaths()
    {
        $filePaths = ["foo/bar.css", "abc/xyz.css"];
        $this->generic->addHeaderCSSFilePaths($filePaths);
        $this->assertEquals($filePaths, $this->generic->getHeaderCSSFilePaths());
    }

    /**
     * Tests adding multiple inline CSS to the header
     */
    public function testAddingMultipleHeaderInlineCSS()
    {
        $cssList = ["body{font-size: 13px;}", "img{border: none;}"];

        foreach($cssList as $css)
        {
            $this->generic->addHeaderInlineCSS($css);
        }

        $this->assertEquals($cssList, $this->generic->getHeaderInlineCSS());
    }

    /**
     * Tests adding multiple inline JavaScript to the header
     */
    public function testAddingMultipleHeaderInlineJavaScript()
    {
        $javaScriptList = ["alert('hi');", "alert('foo');"];

        foreach($javaScriptList as $javaScript)
        {
            $this->generic->addHeaderInlineJavaScript($javaScript);
        }

        $this->assertEquals($javaScriptList, $this->generic->getHeaderInlineJavaScript());
    }

    /**
     * Tests adding multiple JavaScript file paths to the header
     */
    public function testAddingMultipleHeaderJavaScriptPaths()
    {
        $filePaths = ["foo/bar.js", "abc/xyz.js"];
        $this->generic->addHeaderJavaScriptFilePaths($filePaths);
        $this->assertEquals($filePaths, $this->generic->getHeaderJavaScriptFilePaths());
    }

    /**
     * Tests adding a single CSS file path to the footer
     */
    public function testAddingSingleFooterCSSPath()
    {
        $filePath = "foo/bar.css";
        $this->generic->addFooterCSSFilePaths($filePath);
        $this->assertEquals([$filePath], $this->generic->getFooterCSSFilePaths());
    }

    /**
     * Tests adding a single JavaScript file path to the footer
     */
    public function testAddingSingleFooterJavaScriptPath()
    {
        $filePath = "foo/bar.js";
        $this->generic->addFooterJavaScriptFilePaths($filePath);
        $this->assertEquals([$filePath], $this->generic->getFooterJavaScriptFilePaths());
    }

    /**
     * Tests adding a single CSS file path to the header
     */
    public function testAddingSingleHeaderCSSPath()
    {
        $filePath = "foo/bar.css";
        $this->generic->addHeaderCSSFilePaths($filePath);
        $this->assertEquals([$filePath], $this->generic->getHeaderCSSFilePaths());
    }

    /**
     * Tests adding a single JavaScript file path to the header
     */
    public function testAddingSingleHeaderJavaScriptPath()
    {
        $filePath = "foo/bar.js";
        $this->generic->addHeaderJavaScriptFilePaths($filePath);
        $this->assertEquals([$filePath], $this->generic->getHeaderJavaScriptFilePaths());
    }

    /**
     * Tests not passing contents to the constructor
     */
    public function testNotPassingContentsToConstructor()
    {
        $contents = file_get_contents(__DIR__ . "/../../../../../application/rdev/views/pages/files/Generic.html");
        $this->assertEquals($contents, $this->generic->getContents());
    }

    /**
     * Tests passing contents to the constructor
     */
    public function testPassingContentsToConstructor()
    {
        $generic = new Generic("foo");
        $this->assertEquals("foo", $generic->getContents());
    }
} 