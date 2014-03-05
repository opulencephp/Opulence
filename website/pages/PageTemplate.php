<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a website page
 */
namespace RamODev\Website\Pages;

class PageTemplate
{
    /** The string used to denote the beginning and end of a tag name in a template */
    const TAG_PLACEHOLDER_BOOKEND = "%%";

    /** @var string The path to the page template */
    protected $templatePath = "";
    /** @var array The keyed array of tag (placeholder) names to their values */
    protected $tags = array();

    /**
     * @return string
     */
    public function getHTML()
    {
        ob_start();
        require_once($this->templatePath);

        // Replace the tags with their values
        return str_replace(array_keys($this->tags), array_values($this->tags), ob_get_clean());
    }

    /**
     * Sets the value for a tag in the template
     *
     * @param string $name The name of the tag to replace
     * @param mixed $value The value with which to replace the tag name
     */
    public function setTag($name, $value)
    {
        $this->tags[self::TAG_PLACEHOLDER_BOOKEND . $name . self::TAG_PLACEHOLDER_BOOKEND] = $value;
    }

    /**
     * Sets multiple tags' values in the template
     *
     * @param array $namesToValues The mapping of tag names to their respective values
     */
    public function setTags($namesToValues)
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