<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Migrations;

use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;

if (!defined('T_NAME_QUALIFIED')) {
    define('T_NAME_QUALIFIED', 'T_NAME_QUALIFIED');
}

/**
 * Defines the migration file finder
 */
class FileMigrationFinder
{
    /**
     * Recursively finds all migration classes in the order they're to be run
     *
     * @param string|array $paths The path or list of paths to search
     * @return string[] The list of all migration class names
     * @throws InvalidArgumentException|ReflectionException Thrown if the paths are not a string or array
     */
    public function findAll($paths) : array
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        if (!is_array($paths)) {
            throw new InvalidArgumentException('Paths must be a string or array');
        }

        $allClassNames = [];

        foreach ($paths as $path) {
            if (!is_dir($path)) {
                throw new InvalidArgumentException("Path $path is not a directory");
            }

            $fileIter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

            foreach ($fileIter as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $tokens = token_get_all(file_get_contents($file->getRealPath()));
                $allClassNames = array_merge($allClassNames, $this->getClassNamesFromTokens($tokens));
            }
        }

        // Filter out any non-concrete migration classes
        $migrationClassNames = array_filter($allClassNames, function ($className) {
            $reflectionClass = new ReflectionClass($className);

            return $reflectionClass->implementsInterface(IMigration::class) &&
                !$reflectionClass->isInterface() &&
                !$reflectionClass->isAbstract();
        });

        // Sort the migration classes by their creation dates
        usort($migrationClassNames, function (string $a, string $b) {
            return $a::getCreationDate() <=> $b::getCreationDate();
        });

        return $migrationClassNames;
    }

    /**
     * Gets the class names from a list of tokens
     * This will work even if multiple classes are defined in each file
     *
     * @param string[] $tokens The array of tokens
     * @return string[] The names of the classes
     */
    private function getClassNamesFromTokens(array $tokens) : array
    {
        for ($i = 0;$i < count($tokens);$i++) {
            // Skip literals
            if (is_string($tokens[$i])) {
                continue;
            }

            $className = '';

            switch ($tokens[$i][0]) {
                case T_NAMESPACE:
                    $namespace = '';

                    // Collect all the namespace parts and separators
                    while (isset($tokens[++$i][1])) {
                        if (in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING, T_NAME_QUALIFIED])) {
                            $namespace .= $tokens[$i][1];
                        }
                    }

                    break;
                case T_CLASS:
                    $isClassConstant = false;

                    // Scan previous tokens to see if they're double colons, which would mean this is a class constant
                    for ($j = $i - 1;$j >= 0;$j--) {
                        if (!isset($tokens[$j][1])) {
                            break;
                        }

                        if ($tokens[$j][0] === T_DOUBLE_COLON) {
                            $isClassConstant = true;
                            break 2;
                        } elseif ($tokens[$j][0] === T_WHITESPACE) {
                            // Since we found whitespace, then we know this isn't a class constant
                            break;
                        }
                    }

                    // Get the class name
                    while (isset($tokens[++$i][1])) {
                        if ($tokens[$i][0] === T_STRING) {
                            $className .= $tokens[$i][1];
                            break;
                        }
                    }

                    $classNames[] = ltrim($namespace . '\\' . $className, '\\');
                    break 2;
            }
        }

        return $classNames;
    }
}
