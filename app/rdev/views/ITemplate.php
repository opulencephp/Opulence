<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for templates to implement
 */
namespace RDev\Views;

interface ITemplate
{
    /**
     * Gets the uncompiled templates
     *
     * @return string The uncompiled contents
     */
    public function getContents();

    /**
     * Gets the escaped close tag
     *
     * @return string The escaped close tag
     */
    public function getEscapedCloseTag();

    /**
     * Gets the escaped open tag
     *
     * @return string The escaped open tag
     */
    public function getEscapedOpenTag();

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
     * Gets the statement close tag
     *
     * @return string The statement close tag
     */
    public function getStatementCloseTag();

    /**
     * Gets the statement open tag
     *
     * @return string The statement open tag
     */
    public function getStatementOpenTag();

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
     * Gets the unescaped close tag
     *
     * @return string The unescaped close tag
     */
    public function getUnescapedCloseTag();

    /**
     * Gets the unescaped open tag
     *
     * @return string The unescaped open tag
     */
    public function getUnescapedOpenTag();

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
     * Sets the escaped close tag
     *
     * @param string $escapedCloseTag The escaped close tag
     */
    public function setEscapedCloseTag($escapedCloseTag);

    /**
     * Sets the escaped open tag
     *
     * @param string $escapedOpenTag The escaped open tag
     */
    public function setEscapedOpenTag($escapedOpenTag);

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
     * Sets the statement close tag
     *
     * @param string $statementCloseTag The statement close tag
     */
    public function setStatementCloseTag($statementCloseTag);

    /**
     * Sets the statement open tag
     *
     * @param string $statement The statement open tag
     */
    public function setStatementOpenTag($statement);

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
     * Sets the unescaped close tag
     *
     * @param string $unescapedCloseTag The unescaped close tag
     */
    public function setUnescapedCloseTag($unescapedCloseTag);

    /**
     * Sets the unescaped open tag
     *
     * @param string $unescapedOpenTag The unescaped open tag
     */
    public function setUnescapedOpenTag($unescapedOpenTag);

    /**
     * Sets the value for a variable in the template
     *
     * @param string $name The name of the variable whose value we're setting
     *      For example, if we are setting the value of a variable named "$email" in the template, pass in "email"
     * @param mixed $value
     */
    public function setVar($name, $value);

    /**
     * Sets multiple variables' values in the template
     *
     * @param array $namesToValues The mapping of variable names to their respective values
     */
    public function setVars(array $namesToValues);
}