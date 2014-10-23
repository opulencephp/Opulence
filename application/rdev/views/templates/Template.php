<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods common to all website page files
 */
namespace RDev\Views\Templates;
use RDev\Models\Files;
use RDev\Views\Security;

class Template implements ITemplate
{
    /** The default open tag for unescaped tags  */
    const DEFAULT_UNESCAPED_OPEN_TAG = "{{!";
    /** The default close tag for unescaped tags  */
    const DEFAULT_UNESCAPED_CLOSE_TAG = "!}}";
    /** The default open tag for escaped tags  */
    const DEFAULT_ESCAPED_OPEN_TAG = "{{";
    /** The default close tag for escaped tags */
    const DEFAULT_ESCAPED_CLOSE_TAG = "}}";

    /** @var string The uncompiled contents of the template */
    protected $contents = "";
    /** @var array The mapping of tag names to their values */
    protected $tags = [];
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var string The unescaped open tag */
    protected $unescapedOpenTag = self::DEFAULT_UNESCAPED_OPEN_TAG;
    /** @var string The unescaped close tag */
    protected $unescapedCloseTag = self::DEFAULT_UNESCAPED_CLOSE_TAG;
    /** @var string The escaped open tag */
    protected $escapedOpenTag = self::DEFAULT_ESCAPED_OPEN_TAG;
    /** @var string The escaped close tag */
    protected $escapedCloseTag = self::DEFAULT_ESCAPED_CLOSE_TAG;

    /**
     * @param string $contents The contents of the template
     */
    public function __construct($contents = "")
    {
        $this->setContents($contents);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getEscapedCloseTag()
    {
        return $this->escapedCloseTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getEscapedOpenTag()
    {
        return $this->escapedOpenTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getTag($name)
    {
        if(isset($this->tags[$name]))
        {
            return $this->tags[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnescapedCloseTag()
    {
        return $this->unescapedCloseTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnescapedOpenTag()
    {
        return $this->unescapedOpenTag;
    }

    /**
     * {@inheritdoc}
     */
    public function getVar($name)
    {
        if(isset($this->vars[$name]))
        {
            return $this->vars[$name];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function setContents($contents)
    {
        if(!is_string($contents))
        {
            throw new \InvalidArgumentException("Contents are not a string");
        }

        $this->contents = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function setEscapedCloseTag($escapedCloseTag)
    {
        $this->escapedCloseTag = $escapedCloseTag;
    }

    /**
     * {@inheritdoc}
     */
    public function setEscapedOpenTag($escapedOpenTag)
    {
        $this->escapedOpenTag = $escapedOpenTag;
    }

    /**
     * {@inheritdoc}
     */
    public function setTag($name, $value)
    {
        $this->tags[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setTags(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setTag($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setUnescapedCloseTag($unescapedCloseTag)
    {
        $this->unescapedCloseTag = $unescapedCloseTag;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnescapedOpenTag($unescapedOpenTag)
    {
        $this->unescapedOpenTag = $unescapedOpenTag;
    }

    /**
     * {@inheritdoc}
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setVars(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setVar($name, $value);
        }
    }
} 