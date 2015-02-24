<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a wrapper around Composer
 */
namespace RDev\Framework\Composer;
use RDev\Applications;

class Composer
{
    /** @var array The raw config */
    private $rawConfig = [];
    /** @var Applications\Paths The paths of the application */
    private $paths = null;

    /**
     * @param array $config The raw config
     * @param Applications\Paths $paths The paths of the application
     */
    public function __construct(array $config, Applications\Paths $paths)
    {
        $this->rawConfig = $config;
        $this->paths = $paths;
    }

    /**
     * Creates an instance of this class from a raw Composer config file
     *
     * @param Applications\Paths $paths The paths of the application
     * @return Composer An instance of this class
     */
    public static function createFromRawConfig(Applications\Paths $paths)
    {
        $composerPath = $paths["root"] . "/composer.json";

        if(file_exists($composerPath))
        {
            return new Composer(json_decode(file_get_contents($composerPath), true), $paths);
        }

        return new Composer([], $paths);
    }

    /**
     * Gets the value of a property
     *
     * @param string $property The property to get (use periods to denote sub-properties)
     * @return mixed|null The value if it exists, otherwise null
     */
    public function get($property)
    {
        $properties = explode(".", $property);
        $value = $this->rawConfig;

        foreach($properties as $property)
        {
            if(!array_key_exists($property, $value))
            {
                return null;
            }

            $value = $value[$property];
        }

        return $value;
    }

    /**
     * Gets the path from a fully-qualified class name
     *
     * @param string $fullyQualifiedClassName The fully-qualified class name
     * @return string The path
     */
    public function getClassPath($fullyQualifiedClassName)
    {
        $parts = explode("\\", $fullyQualifiedClassName);
        $path = array_slice($parts, 0, -1);
        // The directories are stored in lower case
        $path = array_map("mb_strtolower", $path);
        $path[] = end($parts) . ".php";
        array_unshift($path, $this->paths["app"]);

        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Gets the fully-qualified class name
     *
     * @param string $className The input class name
     * @param string $defaultNamespace The default namespace
     * @return string The fully-qualified class name
     */
    public function getFullyQualifiedClassName($className, $defaultNamespace)
    {
        $rootNamespace = $this->getRootNamespace();

        // If the class name is already fully-qualified
        if(mb_strpos($className, $rootNamespace) === 0)
        {
            return $className;
        }

        return trim($defaultNamespace, "\\") . "\\" . $className;
    }

    /**
     * @return array
     */
    public function getRawConfig()
    {
        return $this->rawConfig;
    }

    /**
     * Gets the root namespace for the application
     *
     * @return string|null The root namespace
     */
    public function getRootNamespace()
    {
        if(($psr4 = $this->get("autoload.psr-4")) === null)
        {
            return null;
        }

        foreach($psr4 as $namespace => $namespacePaths)
        {
            foreach((array)$namespacePaths as $namespacePath)
            {
                // The namespace path should be a subdirectory of the "app" directory
                if(mb_strpos(realpath($this->paths["root"] . "/" . $namespacePath), realpath($this->paths["app"])) === 0)
                {
                    return rtrim($namespace, "\\");
                }
            }
        }

        return null;
    }

    /**
     * Gets the paths of the root namespace for the application
     *
     * @return array|null The root namespace paths
     */
    public function getRootNamespacePaths()
    {
        if(($rootNamespace = $this->getRootNamespace()) === null)
        {
            return null;
        }

        return (array)$this->get("autoload.psr-4")[$rootNamespace . "\\"];
    }
}