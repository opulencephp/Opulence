<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Console;

use Aphiria\IO\FileSystem;
use Aphiria\IO\FileSystemException;
use Closure;
use RuntimeException;

/**
 * Defines the class that's used to compile class files from templates
 */
final class ClassFileCompiler
{
    /** @var string The path to the root directory */
    private string $rootDirPath;
    /** @var array The Composer config, deserialized into an array */
    private array $composerConfig;
    /** @var FileSystem The file system */
    private FileSystem $fileSystem;

    /**
     * @param string $composerConfigPath The path to the Composer config
     * @param FileSystem $fileSystem The filesystem
     * @throws RuntimeException Thrown if the Composer config does not exist
     */
    public function __construct(string $composerConfigPath, FileSystem $fileSystem = null)
    {
        if (!file_exists($composerConfigPath)) {
            throw new RuntimeException("No composer file found at path $composerConfigPath");
        }

        $this->rootDirPath = \dirname($composerConfigPath);
        $this->composerConfig = json_decode(file_get_contents($composerConfigPath), true, 512, JSON_THROW_ON_ERROR);
        $this->fileSystem = $fileSystem ?? new FileSystem();
    }

    /**
     * Compiles a template into a class file
     *
     * @param string $fullyQualifiedClassName The fully-qualified class name to use
     * @param string $templatePath The path to the template file to compile
     * @param Closure $customTagCompiler The function that takes in the template contents and compiles any custom tags
     * @return string The path to the newly created file
     * @throws RuntimeException Thrown if the file already existed
     * @throws FileSystemException Thrown if a file could not be read or written
     */
    public function compile(string $fullyQualifiedClassName, string $templatePath, Closure $customTagCompiler = null): string
    {
        $path = $this->getClassPath($fullyQualifiedClassName);

        if ($this->fileSystem->exists($path)) {
            throw new RuntimeException('File already exists');
        }

        $this->makeDirectories($path);

        $explodedClass = explode('\\', $fullyQualifiedClassName);
        $namespace = implode('\\', array_slice($explodedClass, 0, -1));
        $fullyQualifiedClassName = end($explodedClass);
        $compiledTemplate = str_replace(
            ['{{namespace}}', '{{class}}'],
            [$namespace, $fullyQualifiedClassName],
            $this->fileSystem->read($templatePath)
        );

        if ($customTagCompiler !== null) {
            $compiledTemplate = $customTagCompiler($customTagCompiler);
        }

        $this->fileSystem->write($path, $compiledTemplate);

        return $path;
    }

    /**
     * Gets the value of a property
     *
     * @param string $propertyPath The path to the property to get (use periods to denote sub-properties)
     * @return mixed|null The value if it exists, otherwise null
     */
    private function get(string $propertyPath)
    {
        $properties = explode('.', $propertyPath);
        $value = $this->composerConfig;

        foreach ($properties as $property) {
            if (!array_key_exists($property, $value)) {
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
     * @throws RuntimeException Thrown if no path could be found for the input class name
     */
    private function getClassPath(string $fullyQualifiedClassName) : string
    {
        if (($psr4 = $this->get('autoload.psr-4')) === null) {
            throw new RuntimeException('No PSR-4 section in composer.json');
        }

        $psr4Matches = [];

        // Get all matching PSR-4 namespaces, and keep track of how closely they match our input fully-qualified class
        foreach ($psr4 as $namespace => $namespacePaths) {
            if (\mb_strpos($fullyQualifiedClassName, $namespace) === 0) {
                $psr4Matches[] = ['namespace' => $namespace, 'score' => \mb_strlen($namespace), 'paths' => (array)$namespacePaths];
            }
        }

        if (count($psr4Matches) === 0) {
            throw new RuntimeException("No PSR-4 mappings for class $fullyQualifiedClassName");
        }

        usort($psr4Matches, fn ($a, $b) => $a['score'] <=> $b['score']);
        $bestMatch = $psr4Matches[0];
        $path = "{$this->rootDirPath}/{$bestMatch['paths'][0]}";

        // Grab the remaining part of the fully-qualified class that is not defined in the PSR-4 mappings and build the path
        foreach (explode('\\', ltrim(substr($fullyQualifiedClassName, $bestMatch['score']), '\\')) as $piece) {
            $path .= "/$piece";
        }

        return "$path.php";
    }

    /**
     * Makes the necessary directories for a class
     *
     * @param string $path The fully-qualified class name
     */
    private function makeDirectories(string $path): void
    {
        $directoryName = dirname($path);

        if (!$this->fileSystem->isDirectory($directoryName)) {
            $this->fileSystem->makeDirectory($directoryName, 0777, true);
        }
    }
}
