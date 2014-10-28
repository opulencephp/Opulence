<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the cache for rendered templates
 */
namespace RDev\Views\Templates;
use RDev\Files;

class Cache implements ICache
{
    /** @var Files\FileSystem The file system to use to read cached templates */
    private $fileSystem = null;
    /** @var string The path to store the cached templates at */
    private $path = null;
    /** @var int The number of seconds cached templates should live */
    private $lifetime = self::DEFAULT_LIFETIME;
    /** @var int The chance (out of the total) that garbage collection will be run */
    private $gcChance = self::DEFAULT_GC_CHANCE;
    /** @var int The number the chance will be divided by to calculate the probability */
    private $gcTotal = self::DEFAULT_GC_TOTAL;

    /**
     * @param Files\FileSystem $fileSystem The file system to use to read cached template
     * @param string $path The path to store the cached templates at
     * @param int $lifetime The number of seconds cached templates should live
     * @param int $gcChance The chance (out of the total) that garbage collection will be run
     * @param int $gcTotal The number the chance will be divided by to calculate the probability
     */
    public function __construct(
        Files\FileSystem $fileSystem,
        $path,
        $lifetime = self::DEFAULT_LIFETIME,
        $gcChance = self::DEFAULT_GC_CHANCE,
        $gcTotal = self::DEFAULT_GC_TOTAL
    )
    {
        $this->fileSystem = $fileSystem;
        $this->path = rtrim($path, "/");
        $this->setLifetime($lifetime);
        $this->setGCChance($gcChance, $gcTotal);
    }

    /**
     * Performs some garbage collection
     */
    public function __destruct()
    {
        if(rand(1, $this->gcTotal) <= $this->gcChance)
        {
            $this->gc();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $templatePaths = $this->fileSystem->getFiles($this->path);

        foreach($templatePaths as $templatePath)
        {
            $this->fileSystem->deleteFile($templatePath);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function gc()
    {
        $templatePaths = $this->fileSystem->getFiles($this->path);

        foreach($templatePaths as $templatePath)
        {
            if($this->isExpired($templatePath))
            {
                $this->fileSystem->deleteFile($templatePath);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($unrenderedTemplate, array $variables = [], array $tags = [])
    {
        if(!$this->has($unrenderedTemplate, $variables, $tags))
        {
            return null;
        }

        return $this->fileSystem->read($this->getTemplatePath($unrenderedTemplate, $variables, $tags));
    }

    /**
     * {@inheritdoc}
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function has($unrenderedTemplate, array $variables = [], array $tags = [])
    {
        if(!$this->cachingIsEnabled())
        {
            return false;
        }

        $templatePath = $this->getTemplatePath($unrenderedTemplate, $variables, $tags);
        $exists = $this->fileSystem->exists($templatePath);

        if(!$exists)
        {
            return false;
        }

        // Check the expiration
        if($this->isExpired($templatePath))
        {
            // Do some garbage collection
            $this->fileSystem->deleteFile($templatePath);

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function set($renderedTemplate, $unrenderedTemplate, array $variables = [], array $tags = [])
    {
        if($this->cachingIsEnabled())
        {
            $this->fileSystem->write($this->getTemplatePath($unrenderedTemplate, $variables, $tags), $renderedTemplate);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setGCChance($chance, $total = 100)
    {
        $this->gcChance = $chance;
        $this->gcTotal = $total;
    }

    /**
     * {@inheritdoc}
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
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
     * Gets path to cached template
     *
     * @param string $unrenderedTemplate The unrendered template
     * @param array $variables The list of variables used by this template
     * @param array $tags The list of tag values used by this template
     * @return string The path to the cached template
     */
    private function getTemplatePath($unrenderedTemplate, array $variables, array $tags)
    {
        return $this->path . "/" . md5(http_build_query([
            "u" => $unrenderedTemplate,
            "v" => $variables,
            "t" => $tags
        ]));
    }

    /**
     * Checks whether or not a template path is expired
     *
     * @param string $templatePath The template path to check
     * @return bool True if the path is expired, otherwise false
     */
    private function isExpired($templatePath)
    {
        return $this->fileSystem->getLastModified($templatePath) < new \DateTime("-" . $this->lifetime . " seconds");
    }
}