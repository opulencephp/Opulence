<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Caching;

use DateTime;
use Opulence\Views\IView;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Defines the file cache for compiled views
 */
class FileCache implements ICache
{
    /** @var string The path to store the cached views at */
    private $path = null;
    /** @var int The number of seconds cached views should live */
    private $lifetime = self::DEFAULT_LIFETIME;
    /** @var int The chance (out of the total) that garbage collection will be run */
    private $gcChance = self::DEFAULT_GC_CHANCE;
    /** @var int The number the chance will be divided by to calculate the probability */
    private $gcDivisor = self::DEFAULT_GC_DIVISOR;

    /**
     * @param string|null $path The path to store the cached views at, or null if the path is not yet set
     * @param int $lifetime The number of seconds cached views should live
     * @param int $gcChance The chance (out of the total) that garbage collection will be run
     * @param int $gcDivisor The number the chance will be divided by to calculate the probability
     */
    public function __construct(
        string $path = null,
        int $lifetime = self::DEFAULT_LIFETIME,
        int $gcChance = self::DEFAULT_GC_CHANCE,
        int $gcDivisor = self::DEFAULT_GC_DIVISOR
    ) {
        if ($path !== null) {
            $this->setPath($path);
        }

        $this->lifetime = $lifetime;
        $this->setGCChance($gcChance, $gcDivisor);
    }

    /**
     * Performs some garbage collection
     */
    public function __destruct()
    {
        if (rand(1, $this->gcDivisor) <= $this->gcChance) {
            $this->gc();
        }
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        foreach ($this->getCompiledViewPaths($this->path) as $viewPath) {
            @unlink($viewPath);
        }
    }

    /**
     * @inheritdoc
     */
    public function gc()
    {
        foreach ($this->getCompiledViewPaths($this->path) as $viewPath) {
            if ($this->isExpired($viewPath)) {
                @unlink($viewPath);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function get(IView $view, bool $checkVars = false)
    {
        if (!$this->has($view, $checkVars)) {
            return null;
        }

        return file_get_contents($this->getViewPath($view, $checkVars));
    }

    /**
     * @inheritdoc
     */
    public function has(IView $view, bool $checkVars = false) : bool
    {
        if (!$this->cachingIsEnabled()) {
            return false;
        }

        $viewPath = $this->getViewPath($view, $checkVars);

        if (!file_exists($viewPath)) {
            return false;
        }

        // Check the expiration
        if ($this->isExpired($viewPath)) {
            // Do some garbage collection
            @unlink($viewPath);

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function set(IView $view, string $compiledContents, bool $checkVars = false)
    {
        if ($this->cachingIsEnabled()) {
            file_put_contents($this->getViewPath($view, $checkVars), $compiledContents, 0);
        }
    }

    /**
     * @inheritdoc
     */
    public function setGCChance(int $chance, int $divisor = 100)
    {
        $this->gcChance = $chance;
        $this->gcDivisor = $divisor;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = rtrim($path, '/');

        // Make sure the path exists
        if (!file_exists($this->path)) {
            mkdir($this->path, 0777, false);
            chmod($this->path, 0777);
        }
    }

    /**
     * Gets whether or not caching is enabled
     *
     * @return bool True if caching is enabled, otherwise false
     */
    private function cachingIsEnabled() : bool
    {
        return $this->lifetime > 0;
    }

    /**
     * Gets a list of view file paths that appear
     *
     * @param string $path The path to search
     * @return array The list of view paths
     */
    private function getCompiledViewPaths(string $path) : array
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        $iter->setMaxDepth(0);

        foreach ($iter as $path => $item) {
            if ($item->isFile()) {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Gets path to cached view
     *
     * @param IView $view The view whose cached file path we want
     * @param bool $checkVars Whether or not we want to also check for variable value equivalence when looking up cached views
     * @return string The path to the cached view
     */
    private function getViewPath(IView $view, bool $checkVars) : string
    {
        $data = ['u' => $view->getContents()];

        if ($checkVars) {
            $data['v'] = $view->getVars();
        }

        return $this->path . '/' . md5(http_build_query($data));
    }

    /**
     * Checks whether or not a view path is expired
     *
     * @param string $viewPath The view path to check
     * @return bool True if the path is expired, otherwise false
     */
    private function isExpired(string $viewPath) : bool
    {
        $lastModified = DateTime::createFromFormat('U', filemtime($viewPath));

        return $lastModified < new DateTime('-' . $this->lifetime . ' seconds');
    }
}
