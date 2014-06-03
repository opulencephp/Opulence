<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods common to all website page templates
 */
namespace RDev\Views\Pages;
use RDev\Views;

class Template implements Views\IView
{
    /** The default string used to denote the beginning of a tag name in a template */
    const DEFAULT_OPEN_TAG_PLACEHOLDER = "{{";
    /** The default string used to denote the end of a tag name in a template */
    const DEFAULT_CLOSE_TAG_PLACEHOLDER = "}}";

    /** @var string The path to the template */
    protected $templatePath = "";
    /** @var array The keyed array of tag (placeholder) names to their values */
    protected $tags = [];
    /** @var string The open tag placeholder */
    private $openTagPlaceholder = self::DEFAULT_OPEN_TAG_PLACEHOLDER;
    /** @var string The close tag placeholder */
    private $closeTagPlaceholder = self::DEFAULT_CLOSE_TAG_PLACEHOLDER;

    /**
     * @param string $templatePath The path to the template to use
     */
    public function __construct($templatePath = "")
    {
        $this->setTemplatePath($templatePath);
    }

    /**
     * @return string
     */
    public function getCloseTagPlaceholder()
    {
        return $this->closeTagPlaceholder;
    }

    /**
     * @return string
     */
    public function getOpenTagPlaceholder()
    {
        return $this->openTagPlaceholder;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        $untaggedTemplate = file_get_contents($this->templatePath);
        // Replace the tags with their values
        $callback = function ($tag)
        {
            return $this->openTagPlaceholder . $tag . $this->closeTagPlaceholder;
        };
        $tagsWithBookends = array_map($callback, array_keys($this->tags));
        $taggedTemplate = str_replace($tagsWithBookends, array_values($this->tags), $untaggedTemplate);
        // Remove any left-over, unset tags
        $taggedTemplate = preg_replace("/" .
            preg_quote($this->openTagPlaceholder, "/") .
            "((?!" . preg_quote($this->closeTagPlaceholder, "/") . ").)*" .
            preg_quote($this->closeTagPlaceholder, "/") .
            "/u", "", $taggedTemplate);

        return $taggedTemplate;
    }

    /**
     * @param string $closeTagPlaceholder
     */
    public function setCloseTagPlaceholder($closeTagPlaceholder)
    {
        $this->closeTagPlaceholder = $closeTagPlaceholder;
    }

    /**
     * @param string $openTagPlaceholder
     */
    public function setOpenTagPlaceholder($openTagPlaceholder)
    {
        $this->openTagPlaceholder = $openTagPlaceholder;
    }

    /**
     * Sets the value for a tag in the template
     * If the value was previously set for this tag, it'll be overwritten
     *
     * @param string $name The name of the tag to replace
     * @param mixed $value The value with which to replace the tag name
     */
    public function setTag($name, $value)
    {
        $this->tags[$name] = $value;
    }

    /**
     * Sets multiple tags' values in the template
     *
     * @param array $namesToValues The mapping of tag names to their respective values
     */
    public function setTags(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setTag($name, $value);
        }
    }

    /**
     * @param string $path
     */
    public function setTemplatePath($path)
    {
        $this->templatePath = $path;
    }
} 