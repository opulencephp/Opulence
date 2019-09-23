<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Composer;

/**
 * Defines a wrapper around Composer
 */
final class Composer
{
    /** @var array The raw config */
    private array $rawConfig;
    /** @var string The path to the root of the project */
    private string $rootPath;
    /** @var string The path to the PSR-4 source directory */
    private string $psr4RootPath;

    /**
     * @param array $config The raw config
     * @param string $rootPath The path to the roof of the project
     * @param string $psr4RootPath The path to the PSR-4 source directory
     */
    public function __construct(array $config, string $rootPath, string $psr4RootPath)
    {
        $this->rawConfig = $config;
        $this->rootPath = $rootPath;
        $this->psr4RootPath = $psr4RootPath;
    }

    /**
     * Creates an instance of this class from a raw Composer config file
     *
     * @param string $rootPath The path to the roof of the project
     * @param string $psr4RootPath The path to the PSR-4 source directory
     * @return Composer An instance of this class
     */
    public static function createFromRawConfig(string $rootPath, string $psr4RootPath): Composer
    {
        $composerConfigPath = "$rootPath/composer.json";

        if (file_exists($composerConfigPath)) {
            return new Composer(json_decode(file_get_contents($composerConfigPath), true), $rootPath, $psr4RootPath);
        }

        return new Composer([], $rootPath, $psr4RootPath);
    }

    /**
     * Gets the value of a property
     *
     * @param string $property The property to get (use periods to denote sub-properties)
     * @return mixed|null The value if it exists, otherwise null
     */
    public function get(string $property)
    {
        $properties = explode('.', $property);
        $value = $this->rawConfig;

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
     */
    public function getClassPath(string $fullyQualifiedClassName): string
    {
        $parts = explode('\\', $fullyQualifiedClassName);

        /**
         * If the root namespace does have a directory (eg Project\MyClass lives in src/Project/MyClass.php),
         * then we do include it in the path
         * If the root namespace does not have a directory (eg Project\MyClass lives in src/MyClass.php),
         * then we do not include it in the path (ie don't use "Project" in the path)
         *
         * Note:  This is mainly for backwards-compatibility with the directory structure for v1.0.* of the
         * skeleton project.  This is hacky, but it works.
         */
        if (file_exists(realpath($this->psr4RootPath . '/' . $parts[0]))) {
            $path = array_slice($parts, 0, -1);
        } else {
            $path = array_slice($parts, 1, -1);
        }

        $path[] = end($parts) . '.php';
        array_unshift($path, $this->psr4RootPath);

        return implode(DIRECTORY_SEPARATOR, $path);
    }

    /**
     * Gets the fully-qualified class name
     *
     * @param string $className The input class name
     * @param string $defaultNamespace The default namespace
     * @return string The fully-qualified class name
     */
    public function getFullyQualifiedClassName(string $className, string $defaultNamespace): string
    {
        $rootNamespace = $this->getRootNamespace();

        // If the class name is already fully-qualified
        if (mb_strpos($className, $rootNamespace) === 0) {
            return $className;
        }

        return trim($defaultNamespace, '\\') . '\\' . $className;
    }

    /**
     * @return array
     */
    public function getRawConfig(): array
    {
        return $this->rawConfig;
    }

    /**
     * Gets the root namespace for the application
     *
     * @return string|null The root namespace
     */
    public function getRootNamespace(): ?string
    {
        if (($psr4 = $this->get('autoload.psr-4')) === null) {
            return null;
        }

        foreach ($psr4 as $namespace => $namespacePaths) {
            $realPsr4RootPath = \realpath($this->psr4RootPath);

            foreach ((array)$namespacePaths as $namespacePath) {
                // The namespace path should be a subdirectory of the "src" directory
                $realNamespacePath = \realpath($this->rootPath . '/' . $namespacePath);

                if ($realPsr4RootPath !== false && $realNamespacePath !== false && mb_strpos($realNamespacePath, $realPsr4RootPath) === 0) {
                    return rtrim($namespace, '\\');
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
    public function getRootNamespacePaths(): ?array
    {
        if (($rootNamespace = $this->getRootNamespace()) === null) {
            return null;
        }

        return (array)$this->get('autoload.psr-4')[$rootNamespace . '\\'];
    }
}
