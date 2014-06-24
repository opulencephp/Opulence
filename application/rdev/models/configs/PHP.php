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
    /** The relative path to the project's test directory */
    const RELATIVE_PATH_TO_TEST_DIR = "../../../../tests/application";

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
     * @throws \RuntimeException Thrown if the file path couldn't be found
     */
    public function autoload($qualifiedClassName)
    {
        $explodedFullyQualifiedClassName = explode("\\", $qualifiedClassName);
        $filePath = "";

        if(count($explodedFullyQualifiedClassName) > 0 && in_array($explodedFullyQualifiedClassName[0], self::$rootNamespaces))
        {
            $className = array_pop($explodedFullyQualifiedClassName);
            $explodedPath = array_map("strtolower", $explodedFullyQualifiedClassName);

            if($explodedPath[1] == "tests")
            {
                // Remove "tests" from the path
                unset($explodedPath[1]);
                $explodedPath = array_values($explodedPath);
                $filePath = __DIR__ . "/" . self::RELATIVE_PATH_TO_TEST_DIR . "/" . implode("/", $explodedPath) . "/" . $className . ".php";
            }
            else
            {
                $filePath = __DIR__ . "/" . self::RELATIVE_PATH_TO_APPLICATION_DIR . "/" . implode("/", $explodedPath) . "/" . $className . ".php";
            }
        }
        else
        {
            // Some classes that are built into PHP may not have a source file we can include, so check for one first
            if(file_exists($qualifiedClassName . ".php"))
            {
                $filePath = $qualifiedClassName . ".php";
            }
        }

        if(!file_exists($filePath))
        {
            throw new \RuntimeException("Invalid file path: " . $filePath);
        }

        require_once($filePath);
    }
}

$phpConfig = new PHP();