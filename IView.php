<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for all views to implement
 */
namespace Opulence\Views;

interface IView
{
    /**
     * Gets the uncompiled contents
     *
     * @return string The uncompiled contents
     */
    public function getContents();

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
     * Sets the uncompiled contents of the view
     *
     * @param string $contents The uncompiled contents
     */
    public function setContents($contents);

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