<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the cache for rendered views
 */
namespace Opulence\Views\Caching;
use DateTime;
use Opulence\Files\FileSystem;

class Cache implements ICache
{
    /** @var FileSystem The file system to use to read cached views */
    private $fileSystem = null;
    /** @var string The path to store the cached views at */
    private $path = null;
    /** @var int The number of seconds cached views should live */
    private $lifetime = self::DEFAULT_LIFETIME;
    /** @var int The chance (out of the total) that garbage collection will be run */
    private $gcChance = self::DEFAULT_GC_CHANCE;
    /** @var int The number the chance will be divided by to calculate the probability */
    private $gcDivisor = self::DEFAULT_GC_DIVISOR;

    /**
     * @param FileSystem $fileSystem The file system to use to read cached view
     * @param string|null $path The path to store the cached views at, or null if the path is not yet set
     * @param int $lifetime The number of seconds cached views should live
     * @param int $gcChance The chance (out of the total) that garbage collection will be run
     * @param int $gcDivisor The number the chance will be divided by to calculate the probability
     */
    public function __construct(
        FileSystem $fileSystem,
        $path = null,
        $lifetime = self::DEFAULT_LIFETIME,
        $gcChance = self::DEFAULT_GC_CHANCE,
        $gcDivisor = self::DEFAULT_GC_DIVISOR
    )
    {
        $this->fileSystem = $fileSystem;

        if($path !== null)
        {
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
        if(rand(1, $this->gcDivisor) <= $this->gcChance)
        {
            $this->gc();
        }
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $viewPaths = $this->fileSystem->getFiles($this->path);

        foreach($viewPaths as $viewPath)
        {
            $this->fileSystem->deleteFile($viewPath);
        }
    }

    /**
     * @inheritdoc
     */
    public function gc()
    {
        $viewPaths = $this->fileSystem->getFiles($this->path);

        foreach($viewPaths as $viewPath)
        {
            if($this->isExpired($viewPath))
            {
                $this->fileSystem->deleteFile($viewPath);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function get($unrenderedView, array $variables = [], array $tags = [])
    {
        if(!$this->has($unrenderedView, $variables, $tags))
        {
            return null;
        }

        return $this->fileSystem->read($this->getViewPath($unrenderedView, $variables, $tags));
    }

    /**
     * @inheritdoc
     */
    public function has($unrenderedView, array $variables = [], array $tags = [])
    {
        if(!$this->cachingIsEnabled())
        {
            return false;
        }

        $viewPath = $this->getViewPath($unrenderedView, $variables, $tags);
        $exists = $this->fileSystem->exists($viewPath);

        if(!$exists)
        {
            return false;
        }

        // Check the expiration
        if($this->isExpired($viewPath))
        {
            // Do some garbage collection
            $this->fileSystem->deleteFile($viewPath);

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function set($renderedView, $unrenderedView, array $variables = [], array $tags = [])
    {
        if($this->cachingIsEnabled())
        {
            $this->fileSystem->write($this->getViewPath($unrenderedView, $variables, $tags), $renderedView);
        }
    }

    /**
     * @inheritdoc
     */
    public function setGCChance($chance, $divisor = 100)
    {
        $this->gcChance = $chance;
        $this->gcDivisor = $divisor;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = rtrim($path, "/");

        // Make sure the path exists
        if(!$this->fileSystem->exists($this->path))
        {
            $this->fileSystem->makeDirectory($this->path);
        }
    }

    /**
     * Gets whether or not caching is enabled
     *
     * @return bool True if caching is enabled, otherwise false
     */
    private function cachingIsEnabled()
    {
        return $this->lifetime > 0;
    }

    /**
     * Gets path to cached view
     *
     * @param string $unrenderedView The unrendered view
     * @param array $variables The list of variables used by this view
     * @param array $tags The list of tag values used by this view
     * @return string The path to the cached view
     */
    private function getViewPath($unrenderedView, array $variables, array $tags)
    {
        return $this->path . "/" . md5(http_build_query([
            "u" => $unrenderedView,
            "v" => $variables,
            "t" => $tags
        ]));
    }

    /**
     * Checks whether or not a view path is expired
     *
     * @param string $viewPath The view path to check
     * @return bool True if the path is expired, otherwise false
     */
    private function isExpired($viewPath)
    {
        return $this->fileSystem->getLastModified($viewPath) < new DateTime("-" . $this->lifetime . " seconds");
    }
}