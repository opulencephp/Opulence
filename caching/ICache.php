<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for view caches to implement
 */
namespace Opulence\Views\Caching;

interface ICache
{
    /** The default lifetime of a cached view */
    const DEFAULT_LIFETIME = 3600;
    /** The default chance that garbage collection will be run in this instance */
    const DEFAULT_GC_CHANCE = 1;
    /** The default number the chance will be divided by to calculate the probability */
    const DEFAULT_GC_DIVISOR = 1000;

    /**
     * Flushes all of the rendered views from cache
     */
    public function flush();

    /**
     * Performs garbage collection of expired views
     */
    public function gc();

    /**
     * Gets the unrendered view with the input data
     *
     * @param string $unrenderedView The unrendered view
     * @param array $variables The variables to match on
     * @return string|null The rendered view if it existed, otherwise null
     */
    public function get($unrenderedView, array $variables = []);

    /**
     * Gets whether or not the cache has the unrendered view with the input data
     *
     * @param string $unrenderedView The unrendered view
     * @param array $variables The variables to match on
     * @return bool True if the cache has an unexpired rendered view, otherwise false
     */
    public function has($unrenderedView, array $variables = []);

    /**
     * Stores a rendered view to cache
     *
     * @param string $renderedView The rendered view
     * @param string $unrenderedView The unrendered view
     * @param array $variables The variables to match on
     * @return bool True if successful, otherwise false
     */
    public function set($renderedView, $unrenderedView, array $variables = []);

    /**
     * Sets the chance that garbage collection will be run
     * For example, passing (123, 1000) means you will have a 123/1000 chance of having to perform garbage collection
     *
     * @param int $chance The chance (out of the total) that garbage collection will be run
     * @param int $divisor The number the chance will be divided by to calculate the probability
     */
    public function setGCChance($chance, $divisor = 100);
}