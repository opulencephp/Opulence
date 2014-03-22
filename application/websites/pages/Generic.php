<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a generic page template
 */
namespace RamODev\Websites\Pages;

class Generic extends Template
{
    /** @var string The title of the page */
    protected $title = "";
    /** @var array The meta keywords of the page */
    protected $metaKeywords = array();
    /** @var string The meta description of the page */
    protected $metaDescription = "";
    /** @var string The path to the favicon */
    protected $faviconPath = "";
    /** @var array The list of header CSS file paths */
    protected $headerCSSFilePaths = array();
    /** @var array The list of inline header CSS */
    protected $headerInlineCSS = array();
    /** @var array The list of footer CSS file paths */
    protected $footerCSSFilePaths = array();
    /** @var array The list of inline footer CSS */
    protected $footerInlineCSS = array();
    /** @var array The list of header JavaScript file paths */
    protected $headerJavaScriptFilePaths = array();
    /** @var array The list of inline header JavaScript */
    protected $headerInlineJavaScript = array();
    /** @var array The list of footer JavaScript file paths */
    protected $footerJavaScriptFilePaths = array();
    /** @var array The list of inline footer JavaScript */
    protected $footerInlineJavaScript = array();

    public function __construct()
    {
        parent::__construct(__DIR__ . "/templates/Generic.html");
    }

    /**
     * Adds CSS file paths to the footer
     *
     * @param string|array $paths The path or list of paths of CSS files to include in the footer
     */
    public function addFooterCSSFilePaths($paths)
    {
        if(is_string($paths))
        {
            $paths = array($paths);
        }

        $this->footerCSSFilePaths = array_merge($this->footerCSSFilePaths, $paths);
    }

    /**
     * Adds inline CSS to the footer
     *
     * @param string $css The CSS to add
     */
    public function addFooterInlineCSS($css)
    {
        $this->footerInlineCSS[] = $css;
    }

    /**
     * Adds inline JavaScript to the footer
     *
     * @param string $javaScript The JavaScript to add
     */
    public function addFooterInlineJavaScript($javaScript)
    {
        $this->footerInlineJavaScript[] = $javaScript;
    }

    /**
     * Adds JavaScript file paths to the footer
     *
     * @param string|array $paths The path or list of paths of JavaScript files to include in the footer
     */
    public function addFooterJavaScriptFilePaths($paths)
    {
        if(is_string($paths))
        {
            $paths = array($paths);
        }

        $this->footerJavaScriptFilePaths = array_merge($this->footerJavaScriptFilePaths, $paths);
    }

    /**
     * Adds CSS file paths to the header
     *
     * @param string|array $paths The path or list of paths of CSS files to include in the header
     */
    public function addHeaderCSSFilePaths($paths)
    {
        if(is_string($paths))
        {
            $paths = array($paths);
        }

        $this->headerCSSFilePaths = array_merge($this->headerCSSFilePaths, $paths);
    }

    /**
     * Adds inline CSS to the header
     *
     * @param string $css The CSS to add
     */
    public function addHeaderInlineCSS($css)
    {
        $this->headerInlineCSS[] = $css;
    }

    /**
     * Adds inline JavaScript to the header
     *
     * @param string $javaScript The JavaScript to add
     */
    public function addHeaderInlineJavaScript($javaScript)
    {
        $this->headerInlineJavaScript[] = $javaScript;
    }

    /**
     * Adds JavaScript file paths to the header
     *
     * @param string|array $paths The path or list of paths of JavaScript files to include in the header
     */
    public function addHeaderJavaScriptFilePaths($paths)
    {
        if(is_string($paths))
        {
            $paths = array($paths);
        }

        $this->headerJavaScriptFilePaths = array_merge($this->headerJavaScriptFilePaths, $paths);
    }

    /**
     * @param array|string $metaKeywords
     */
    public function addMetaKeywords($metaKeywords)
    {
        if(is_string($metaKeywords))
        {
            $metaKeywords = array($metaKeywords);
        }

        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return array
     */
    public function getFooterCSSFilePaths()
    {
        return $this->footerCSSFilePaths;
    }

    /**
     * @return array
     */
    public function getFooterInlineCSS()
    {
        return $this->footerInlineCSS;
    }

    /**
     * @return array
     */
    public function getFooterInlineJavaScript()
    {
        return $this->footerInlineJavaScript;
    }

    /**
     * @return array
     */
    public function getFooterJavaScriptFilePaths()
    {
        return $this->footerJavaScriptFilePaths;
    }

    /**
     * @return array
     */
    public function getHeaderCSSFilePaths()
    {
        return $this->headerCSSFilePaths;
    }

    /**
     * @return array
     */
    public function getHeaderInlineCSS()
    {
        return $this->headerInlineCSS;
    }

    /**
     * @return array
     */
    public function getHeaderInlineJavaScript()
    {
        return $this->headerInlineJavaScript;
    }

    /**
     * @return array
     */
    public function getHeaderJavaScriptFilePaths()
    {
        return $this->headerJavaScriptFilePaths;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        if(!empty($this->title))
        {
            $this->setTag("title", '<title>' . htmlentities($this->title) . '</title>');
        }

        if(!empty($this->metaDescription))
        {
            $this->setTag("metaDescription", '<meta name="description" content="' . htmlentities($this->metaDescription) . '">');
        }

        if(count($this->metaKeywords) > 0)
        {
            // Filter the keywords before displaying them on the page
            $filteredMetaKeywords = array_map("htmlentities", $this->metaKeywords);
            $this->setTag("metaKeywords", '<meta name="keywords" content="' . implode(",", $filteredMetaKeywords) . '">');
        }

        if(!empty($this->faviconPath))
        {
            $this->setTag("favicon", '<link rel="shortcut icon" href="' . $this->faviconPath . '">');
        }

        // Add any CSS/JavaScript to our template
        if(count($this->headerCSSFilePaths) > 0)
        {
            $this->setTag("headerCSSFilePaths", $this->getHTMLForCSSFilePaths($this->headerCSSFilePaths));
        }

        if(count($this->headerJavaScriptFilePaths) > 0)
        {
            $this->setTag("headerJavaScriptFilePaths", $this->getHTMLForJavaScriptFilePaths($this->headerJavaScriptFilePaths));
        }

        if(count($this->footerCSSFilePaths) > 0)
        {
            $this->setTag("footerCSSFilePaths", $this->getHTMLForCSSFilePaths($this->footerCSSFilePaths));
        }

        if(count($this->footerJavaScriptFilePaths) > 0)
        {
            $this->setTag("footerJavaScriptFilePaths", $this->getHTMLForJavaScriptFilePaths($this->footerJavaScriptFilePaths));
        }

        if(count($this->headerInlineCSS) > 0)
        {
            $this->setTag("headerInlineCSS", $this->getHTMLForInlineCSS($this->headerInlineCSS));
        }

        if(count($this->headerInlineJavaScript) > 0)
        {
            $this->setTag("headerInlineJavaScript", $this->getHTMLForInlineJavaScript($this->headerInlineJavaScript));
        }

        if(count($this->footerInlineCSS) > 0)
        {
            $this->setTag("footerInlineCSS", $this->getHTMLForInlineCSS($this->footerInlineCSS));
        }

        if(count($this->footerInlineJavaScript) > 0)
        {
            $this->setTag("footerInlineJavaScript", $this->getHTMLForInlineJavaScript($this->footerInlineJavaScript));
        }

        return parent::getOutput();
    }

    /**
     * Returns HTML that can be used to link to CSS
     *
     * @param array $filePaths The list of CSS file paths
     * @return string The HTML that will link to CSS files
     */
    private function getHTMLForCSSFilePaths($filePaths)
    {
        return '<link type="text/css" rel="stylesheet" href="' . implode('"><link type="text/css" rel="stylesheet" href="', $filePaths) . '">';
    }

    /**
     * Returns HTML that can be used to link to JavaScript
     *
     * @param array $filePaths The list of JavaScript file paths
     * @return string The HTML that will link to CSS JavaScript
     */
    private function getHTMLForJavaScriptFilePaths($filePaths)
    {
        return '<script type="text/javascript" src="' . implode('"></script><script type="text/javascript" src="', $filePaths) . '"></script>';
    }

    /**
     * Returns HTML that can be used to display inline CSS
     *
     * @param array $listOfCSS The list of inline CSS
     * @return string The HTML that will display the inline CSS
     */
    private function getHTMLForInlineCSS($listOfCSS)
    {
        return '<style type="text/css">' . implode("\n", $listOfCSS) . '</style>';
    }

    /**
     * Returns HTML that can be used to display inline JavaScript
     *
     * @param array $listOfJavaScript The list of inline JavaScript
     * @return string The HTML that will display the inline JavaScript
     */
    private function getHTMLForInlineJavaScript($listOfJavaScript)
    {
        return '<script type="text/javascript">' . implode("\n", $listOfJavaScript) . '</script>';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @param string $faviconPath
     */
    public function setFaviconPath($faviconPath)
    {
        $this->faviconPath = $faviconPath;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }
} 