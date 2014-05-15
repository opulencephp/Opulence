<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines PHP configuration settings
 */
namespace RDev\Application\Shared\Models\Configs;

class PHP
{
    /** The relative path to the project's root directory */
    const RELATIVE_PATH_TO_PROJECT_ROOT_DIR = "../../../..";
    /** The root namespace */
    const ROOT_NAMESPACE = "RDev";

    public function __construct()
    {
        // Register our autoloader
        spl_autoload_register(array($this, "autoload"));
    }

    /**
     * Automatically loads the input class name's source file
     *
     * @param string $qualifiedClassName The fully-qualified name of the class to load
     */
    public function autoload($qualifiedClassName)
    {
        $explodedFullyQualifiedClassName = explode("\\", $qualifiedClassName);

        // Get rid of our root namespace because we don't actually have a folder called that
        if(count($explodedFullyQualifiedClassName) > 0 && $explodedFullyQualifiedClassName[0] == self::ROOT_NAMESPACE)
        {
            array_shift($explodedFullyQualifiedClassName);

            $className = array_pop($explodedFullyQualifiedClassName);
            $explodedPath = array_map("strtolower", $explodedFullyQualifiedClassName);

            require_once(__DIR__ . "/" . self::RELATIVE_PATH_TO_PROJECT_ROOT_DIR . "/" . implode("/", $explodedPath) . "/" . $className . ".php");
        }
        else
        {
            require_once($qualifiedClassName . ".php");
        }
    }
}

$phpConfig = new PHP();