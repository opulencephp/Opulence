<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for views to implement
 */
namespace Opulence\Views;

interface IFortuneView extends IView
{
    /** The directive delimiter */
    const DELIMITER_TYPE_DIRECTIVE = 1;
    /** The sanitized tag delimiter */
    const DELIMITER_TYPE_SANITIZED_TAG = 2;
    /** The unsanitized tag delimiter */
    const DELIMITER_TYPE_UNSANITIZED_TAG = 3;

    /**
     * Gets the open and close delimiters for a particular type
     *
     * @param mixed $type The type of delimiter to get
     * @return array An array containing the open and close delimiters
     */
    public function getDelimiters($type);

    /**
     * Gets the parent view if there is one
     *
     * @return IFortuneView|null The parent view if there is one, otherwise null
     */
    public function getParent();

    /**
     * Gets the contents of a view part
     *
     * @param string $name The name of the view part to get
     * @return string The contents of the view part
     */
    public function getPart($name);

    /**
     * Gets the list of view parts
     *
     * @return array The part name => content mappings
     */
    public function getParts();

    /**
     * Gets the value for a tag
     *
     * @param string $name The name of the tag to get
     * @return mixed|null The value of the tag if it exists, otherwise null
     */
    public function getTag($name);

    /**
     * Gets the list of tags defined in this view
     *
     * @return array The tag name => value mappings
     */
    public function getTags();

    /**
     * Prepares the view for compiling
     */
    public function prepare();

    /**
     * Sets the values for a delimiter type
     *
     * @param mixed $type The type of delimiter to set
     * @param array $values An array containing the open and close delimiter values
     */
    public function setDelimiters($type, array $values);

    /**
     * Sets the parent of this view
     *
     * @param IFortuneView $parent The parent of this view
     */
    public function setParent(IFortuneView $parent);

    /**
     * Sets the content of a view part
     *
     * @param string $name The name of the part to set
     * @param string $content The content of the part
     */
    public function setPart($name, $content);

    /**
     * Sets multiple parts' contents in the view
     *
     * @param array $namesToContents The mapping of part names to their respective values
     */
    public function setParts(array $namesToContents);

    /**
     * Sets the value for a tag in the view
     * If the value was previously set for this tag, it'll be overwritten
     *
     * @param string $name The name of the tag to replace
     * @param mixed $value The value with which to replace the tag name
     */
    public function setTag($name, $value);

    /**
     * Sets multiple tags' values in the view
     *
     * @param array $namesToValues The mapping of tag names to their respective values
     */
    public function setTags(array $namesToValues);
}