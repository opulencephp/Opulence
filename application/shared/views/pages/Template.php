<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines methods common to all website page templates
 */
namespace RDev\Application\Shared\Views\Pages;
use RDev\Application\Shared\Views;

class Template implements Views\IView
{
    /** The string used to denote the beginning and end of a tag name in a template */
    const TAG_PLACEHOLDER_BOOKEND = "%%";

    /** @var string The path to the template */
    protected $templatePath = "";
    /** @var array The keyed array of tag (placeholder) names to their values */
    protected $tags = [];

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
    public function getOutput()
    {
        $untaggedTemplate = file_get_contents($this->templatePath);
        // Replace the tags with their values
        $taggedTemplate = str_replace(array_keys($this->tags), array_values($this->tags), $untaggedTemplate);
        // Remove any left-over, unset tags
        $taggedTemplate = preg_replace("/" . self::TAG_PLACEHOLDER_BOOKEND . "((?!%%).)*" . self::TAG_PLACEHOLDER_BOOKEND . "/u", "", $taggedTemplate);

        return $taggedTemplate;
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
        $this->tags[self::TAG_PLACEHOLDER_BOOKEND . $name . self::TAG_PLACEHOLDER_BOOKEND] = $value;
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