<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines PHP settings
 */
spl_autoload_register(function ($qualifiedClassName)
{
    /** The list of root namespaces in this project */
    $rootNamespaces = ["RDev", "TBA"];
    /** The relative path to the project's application directory */
    $relativePathToApplicationDir = "../application";
    /** The relative path to the project's test directory */
    $relativePathToTestDir = "../tests/application";

    $explodedFullyQualifiedClassName = explode("\\", $qualifiedClassName);
    $filePath = "";

    if(count($explodedFullyQualifiedClassName) > 0 && in_array($explodedFullyQualifiedClassName[0], $rootNamespaces))
    {
        $className = array_pop($explodedFullyQualifiedClassName);
        $explodedPath = array_map("strtolower", $explodedFullyQualifiedClassName);

        if($explodedPath[1] == "tests")
        {
            // Remove "tests" from the path
            unset($explodedPath[1]);
            $explodedPath = array_values($explodedPath);
            $filePath = __DIR__ . DIRECTORY_SEPARATOR . $relativePathToTestDir . DIRECTORY_SEPARATOR .
                implode(DIRECTORY_SEPARATOR, $explodedPath) . DIRECTORY_SEPARATOR . $className . ".php";
        }
        else
        {
            $filePath = __DIR__ . DIRECTORY_SEPARATOR . $relativePathToApplicationDir . DIRECTORY_SEPARATOR .
                implode(DIRECTORY_SEPARATOR, $explodedPath) . DIRECTORY_SEPARATOR . $className . ".php";
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

    if(!empty($filePath))
    {
        if(!file_exists($filePath))
        {
            throw new \RuntimeException("Invalid file path: " . $filePath);
        }

        require_once($filePath);
    }
});