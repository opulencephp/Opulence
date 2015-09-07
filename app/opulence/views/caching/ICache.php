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
     * Flushes all of the compiled views from cache
     */
    public function flush();

    /**
     * Performs garbage collection of expired views
     */
    public function gc();

    /**
     * Gets the uncompiled view with the input data
     *
     * @param string $uncompiledView The uncompiled view
     * @param array $variables The variables to match on
     * @return string|null The compiled view if it existed, otherwise null
     */
    public function get($uncompiledView, array $variables = []);

    /**
     * Gets whether or not the cache has the uncompiled view with the input data
     *
     * @param string $uncompiledView The uncompiled view
     * @param array $variables The variables to match on
     * @return bool True if the cache has an unexpired compiled view, otherwise false
     */
    public function has($uncompiledView, array $variables = []);

    /**
     * Stores a compiled view to cache
     *
     * @param string $compiledView The compiled view
     * @param string $uncompiledView The uncompiled view
     * @param array $variables The variables to match on
     * @return bool True if successful, otherwise false
     */
    public function set($compiledView, $uncompiledView, array $variables = []);

    /**
     * Sets the chance that garbage collection will be run
     * For example, passing (123, 1000) means you will have a 123/1000 chance of having to perform garbage collection
     *
     * @param int $chance The chance (out of the total) that garbage collection will be run
     * @param int $divisor The number the chance will be divided by to calculate the probability
     */
    public function setGCChance($chance, $divisor = 100);
}