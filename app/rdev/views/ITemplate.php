<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for templates to implement
 */
namespace RDev\Views;

interface ITemplate
{
    /** The directive delimiter */
    const DELIMITER_TYPE_DIRECTIVE = 1;
    /** The sanitized tag delimiter */
    const DELIMITER_TYPE_SANITIZED_TAG = 2;
    /** The unsanitized tag delimiter */
    const DELIMITER_TYPE_UNSANITIZED_TAG = 3;

    /**
     * Gets the uncompiled contents
     *
     * @return string The uncompiled contents
     */
    public function getContents();

    /**
     * Gets the open and close delimiters for a particular type
     *
     * @param mixed $type The type of delimiter to get
     * @return array An array containing the open and close delimiters
     */
    public function getDelimiters($type);

    /**
     * Gets the parent template if there is one
     *
     * @return ITemplate|null The parent template if there is one, otherwise null
     */
    public function getParent();

    /**
     * Gets the contents of a template part
     *
     * @param string $name The name of the template part to get
     * @return string The contents of the template part
     */
    public function getPart($name);

    /**
     * Gets the list of template parts
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
     * Gets the list of tags defined in this template
     *
     * @return array The tag name => value mappings
     */
    public function getTags();

    /**
     * Gets the value for a variable
     *
     * @param string $name The name of the variable to get
     * @return mixed|null The value of the variable if it exists, otherwise null
     */
    public function getVar($name);

    /**
     * Gets the list of variables defined in this template
     *
     * @return array The variable name => value mappings
     */
    public function getVars();

    /**
     * Prepares the template for compiling
     */
    public function prepare();

    /**
     * Sets the uncompiled contents of the template
     *
     * @param string $contents The uncompiled contents
     */
    public function setContents($contents);

    /**
     * Sets the values for a delimiter type
     *
     * @param mixed $type The type of delimiter to set
     * @param array $values An array containing the open and close delimiter values
     */
    public function setDelimiters($type, array $values);

    /**
     * Sets the parent of this template
     *
     * @param ITemplate $parent The parent of this template
     */
    public function setParent(ITemplate $parent);

    /**
     * Sets the content of a template part
     *
     * @param string $name The name of the part to set
     * @param string $content The content of the part
     */
    public function setPart($name, $content);

    /**
     * Sets multiple parts' contents in the template
     *
     * @param array $namesToContents The mapping of part names to their respective values
     */
    public function setParts(array $namesToContents);

    /**
     * Sets the value for a tag in the template
     * If the value was previously set for this tag, it'll be overwritten
     *
     * @param string $name The name of the tag to replace
     * @param mixed $value The value with which to replace the tag name
     */
    public function setTag($name, $value);

    /**
     * Sets multiple tags' values in the template
     *
     * @param array $namesToValues The mapping of tag names to their respective values
     */
    public function setTags(array $namesToValues);

    /**
     * Sets the value for a variable in the template
     *
     * @param string $name The name of the variable whose value we're setting
     *      For example, if we are setting the value of a variable named "$email" in the template, pass in "email"
     * @param mixed $value The value of the variable
     */
    public function setVar($name, $value);

    /**
     * Sets multiple variables' values in the template
     *
     * @param array $namesToValues The mapping of variable names to their respective values
     */
    public function setVars(array $namesToValues);
}