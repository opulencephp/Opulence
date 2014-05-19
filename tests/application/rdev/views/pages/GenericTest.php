<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the generic page template
 */
namespace RDev\Views\Pages;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding inline CSS to the footer
     */
    public function testAddingFooterInlineCSS()
    {
        $page = new Generic();
        $css = "body{font-size: 13px;}";
        $page->addFooterInlineCSS($css);
        $this->assertEquals([$css], $page->getFooterInlineCSS());
    }

    /**
     * Tests adding inline JavaScript to the footer
     */
    public function testAddingFooterInlineJavaScript()
    {
        $page = new Generic();
        $javaScript = "alert('hi');";
        $page->addFooterInlineJavaScript($javaScript);
        $this->assertEquals([$javaScript], $page->getFooterInlineJavaScript());
    }

    /**
     * Tests adding multiple inline CSS to the header
     */
    public function testAddingHeaderInlineCSS()
    {
        $page = new Generic();
        $css = "body{font-size: 13px;}";
        $page->addHeaderInlineCSS($css);
        $this->assertEquals([$css], $page->getHeaderInlineCSS());
    }

    /**
     * Tests adding inline JavaScript to the header
     */
    public function testAddingHeaderInlineJavaScript()
    {
        $page = new Generic();
        $javaScript = "alert('hi');";
        $page->addHeaderInlineJavaScript($javaScript);
        $this->assertEquals([$javaScript], $page->getHeaderInlineJavaScript());
    }

    /**
     * Tests adding multiple CSS file paths to the footer
     */
    public function testAddingMultipleFooterCSSPaths()
    {
        $page = new Generic();
        $filePaths = ["foo/bar.css", "abc/xyz.css"];
        $page->addFooterCSSFilePaths($filePaths);
        $this->assertEquals($filePaths, $page->getFooterCSSFilePaths());
    }

    /**
     * Tests adding multiple inline CSS to the footer
     */
    public function testAddingMultipleFooterInlineCSS()
    {
        $page = new Generic();
        $cssList = ["body{font-size: 13px;}", "img{border: none;}"];

        foreach($cssList as $css)
        {
            $page->addFooterInlineCSS($css);
        }

        $this->assertEquals($cssList, $page->getFooterInlineCSS());
    }

    /**
     * Tests adding multiple inline JavaScript to the footer
     */
    public function testAddingMultipleFooterInlineJavaScript()
    {
        $page = new Generic();
        $javaScriptList = ["alert('hi');", "alert('foo');"];

        foreach($javaScriptList as $javaScript)
        {
            $page->addFooterInlineJavaScript($javaScript);
        }

        $this->assertEquals($javaScriptList, $page->getFooterInlineJavaScript());
    }

    /**
     * Tests adding multiple JavaScript file paths to the footer
     */
    public function testAddingMultipleFooterJavaScriptPaths()
    {
        $page = new Generic();
        $filePaths = ["foo/bar.js", "abc/xyz.js"];
        $page->addFooterJavaScriptFilePaths($filePaths);
        $this->assertEquals($filePaths, $page->getFooterJavaScriptFilePaths());
    }

    /**
     * Tests adding multiple CSS file paths to the header
     */
    public function testAddingMultipleHeaderCSSPaths()
    {
        $page = new Generic();
        $filePaths = ["foo/bar.css", "abc/xyz.css"];
        $page->addHeaderCSSFilePaths($filePaths);
        $this->assertEquals($filePaths, $page->getHeaderCSSFilePaths());
    }

    /**
     * Tests adding multiple inline CSS to the header
     */
    public function testAddingMultipleHeaderInlineCSS()
    {
        $page = new Generic();
        $cssList = ["body{font-size: 13px;}", "img{border: none;}"];

        foreach($cssList as $css)
        {
            $page->addHeaderInlineCSS($css);
        }

        $this->assertEquals($cssList, $page->getHeaderInlineCSS());
    }

    /**
     * Tests adding multiple inline JavaScript to the header
     */
    public function testAddingMultipleHeaderInlineJavaScript()
    {
        $page = new Generic();
        $javaScriptList = ["alert('hi');", "alert('foo');"];

        foreach($javaScriptList as $javaScript)
        {
            $page->addHeaderInlineJavaScript($javaScript);
        }

        $this->assertEquals($javaScriptList, $page->getHeaderInlineJavaScript());
    }

    /**
     * Tests adding multiple JavaScript file paths to the header
     */
    public function testAddingMultipleHeaderJavaScriptPaths()
    {
        $page = new Generic();
        $filePaths = ["foo/bar.js", "abc/xyz.js"];
        $page->addHeaderJavaScriptFilePaths($filePaths);
        $this->assertEquals($filePaths, $page->getHeaderJavaScriptFilePaths());
    }

    /**
     * Tests adding a single CSS file path to the footer
     */
    public function testAddingSingleFooterCSSPath()
    {
        $page = new Generic();
        $filePath = "foo/bar.css";
        $page->addFooterCSSFilePaths($filePath);
        $this->assertEquals([$filePath], $page->getFooterCSSFilePaths());
    }

    /**
     * Tests adding a single JavaScript file path to the footer
     */
    public function testAddingSingleFooterJavaScriptPath()
    {
        $page = new Generic();
        $filePath = "foo/bar.js";
        $page->addFooterJavaScriptFilePaths($filePath);
        $this->assertEquals([$filePath], $page->getFooterJavaScriptFilePaths());
    }

    /**
     * Tests adding a single CSS file path to the header
     */
    public function testAddingSingleHeaderCSSPath()
    {
        $page = new Generic();
        $filePath = "foo/bar.css";
        $page->addHeaderCSSFilePaths($filePath);
        $this->assertEquals([$filePath], $page->getHeaderCSSFilePaths());
    }

    /**
     * Tests adding a single JavaScript file path to the header
     */
    public function testAddingSingleHeaderJavaScriptPath()
    {
        $page = new Generic();
        $filePath = "foo/bar.js";
        $page->addHeaderJavaScriptFilePaths($filePath);
        $this->assertEquals([$filePath], $page->getHeaderJavaScriptFilePaths());
    }
} 