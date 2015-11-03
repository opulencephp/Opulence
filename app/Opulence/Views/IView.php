<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views;

/**
 * Defines the interface for all views to implement
 */
interface IView
{
    /** The directive delimiter */
    const DELIMITER_TYPE_DIRECTIVE = 1;
    /** The sanitized tag delimiter */
    const DELIMITER_TYPE_SANITIZED_TAG = 2;
    /** The unsanitized tag delimiter */
    const DELIMITER_TYPE_UNSANITIZED_TAG = 3;
    /** The comment delimiter */
    const DELIMITER_TYPE_COMMENT = 4;

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
     * Gets the path of the raw view
     *
     * @return string The path of the raw view
     */
    public function getPath();

    /**
     * Gets the value for a variable
     *
     * @param string $name The name of the variable to get
     * @return mixed|null The value of the variable if it exists, otherwise null
     */
    public function getVar($name);

    /**
     * Gets the list of variables defined in this view
     *
     * @return array The variable name => value mappings
     */
    public function getVars();

    /**
     * Gets whether or not a variable is set in the view
     *
     * @param string $name The name of the variable to search for
     * @return bool True if the view as the variable, otherwise false
     */
    public function hasVar($name);

    /**
     * Sets the uncompiled contents of the view
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
     * Sets the path of the raw view
     *
     * @param string $path The path of the raw view
     */
    public function setPath($path);

    /**
     * Sets the value for a variable in the view
     *
     * @param string $name The name of the variable whose value we're setting
     *      For example, if we are setting the value of a variable named "$email" in the view, pass in "email"
     * @param mixed $value The value of the variable
     */
    public function setVar($name, $value);

    /**
     * Sets multiple variables' values in the view
     *
     * @param array $namesToValues The mapping of variable names to their respective values
     */
    public function setVars(array $namesToValues);

}