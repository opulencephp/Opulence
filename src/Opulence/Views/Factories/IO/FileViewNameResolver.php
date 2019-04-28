<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Views\Factories\IO;

use InvalidArgumentException;

/**
 * Defines the view name resolver that uses file system files to store views
 */
class FileViewNameResolver implements IViewNameResolver
{
    /** @var array The mapping of paths to their priorities */
    protected $paths = [];
    /** @var array The mapping of extensions to their priorities */
    protected $extensions = [];

    /**
     * @inheritdoc
     */
    public function registerExtension(string $extension, int $priority = -1): void
    {
        $this->extensions[ltrim($extension, '.')] = $priority;
    }

    /**
     * @inheritdoc
     */
    public function registerPath(string $path, int $priority = -1): void
    {
        $this->paths[rtrim($path, '/')] = $priority;
    }

    /**
     * @inheritdoc
     */
    public function resolve(string $name): string
    {
        $sortedExtensions = $this->sortByPriority($this->extensions);
        $sortedPaths = $this->sortByPriority($this->paths);

        if ($this->nameHasExtension($name, $sortedExtensions)) {
            foreach ($sortedPaths as $path) {
                $fullPath = "$path/$name";

                if (file_exists($fullPath)) {
                    return $fullPath;
                }
            }
        } else {
            foreach ($sortedPaths as $path) {
                foreach ($sortedExtensions as $extension) {
                    $fullPath = "$path/$name.$extension";

                    if (file_exists($fullPath)) {
                        return $fullPath;
                    }
                }
            }
        }

        throw new InvalidArgumentException("No view found with name \"$name\"");
    }

    /**
     * Gets whether or not a name has an extension
     *
     * @param string $name The name to check
     * @param array $sortedExtensions The list of sorted extensions to check against
     * @return bool True if the name has an extension, otherwise false
     */
    protected function nameHasExtension(string $name, array $sortedExtensions): bool
    {
        foreach ($sortedExtensions as $extension) {
            $lengthDifference = strlen($name) - strlen($extension);

            if ($lengthDifference > 0 && strpos($name, $extension, $lengthDifference) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sorts a list whose values are priorities
     *
     * @param array $list The list to sort
     * @return array The sorted list
     */
    protected function sortByPriority(array $list): array
    {
        $nonPriorityItems = [];
        $priorityItems = [];

        foreach ($list as $key => $priority) {
            if ($priority == -1) {
                $nonPriorityItems[] = $key;
            } else {
                $priorityItems[$key] = $priority;
            }
        }

        asort($priorityItems);

        return array_merge(array_keys($priorityItems), $nonPriorityItems);
    }
}
