<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines PHP configuration settings
 */
namespace RamODev\Configs;

class PHPConfig
{
    /** The path to our root directory */
    const ROOT_DIR = "/var/www/html";
    /** The root namespace */
    const ROOT_NAMESPACE = "RamODev";

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

            require_once(self::ROOT_DIR . "/" . implode("/", $explodedPath) . "/" . $className . ".php");
        }
        else
        {
            require_once($qualifiedClassName . ".php");
        }
    }
}

$phpConfig = new PHPConfig();