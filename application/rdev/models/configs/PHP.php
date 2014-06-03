<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines PHP configuration settings
 */
namespace RDev\Models\Configs;

class PHP
{
    /** The relative path to the project's application directory */
    const RELATIVE_PATH_TO_APPLICATION_DIR = "../../..";

    /** The root namespaces */
    private static $rootNamespaces = ["RDev", "TBA"];

    public function __construct()
    {
        // Register our autoloader
        spl_autoload_register([$this, "autoload"]);
    }

    /**
     * Automatically loads the input class name's source file
     *
     * @param string $qualifiedClassName The fully-qualified name of the class to load
     */
    public function autoload($qualifiedClassName)
    {
        $explodedFullyQualifiedClassName = explode("\\", $qualifiedClassName);

        if(count($explodedFullyQualifiedClassName) > 0 && in_array($explodedFullyQualifiedClassName[0], self::$rootNamespaces))
        {
            $className = array_pop($explodedFullyQualifiedClassName);
            $explodedPath = array_map("strtolower", $explodedFullyQualifiedClassName);

            require_once(__DIR__ . "/" . self::RELATIVE_PATH_TO_APPLICATION_DIR . "/" . implode("/", $explodedPath) . "/" . $className . ".php");
        }
        else
        {
            // Some classes that are built into PHP may not have a source file we can include, so check for one first
            if(file_exists($qualifiedClassName . ".php"))
            {
                require_once($qualifiedClassName . ".php");
            }
        }
    }
}

$phpConfig = new PHP();