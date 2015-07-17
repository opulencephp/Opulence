<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for template caches to implement
 */
namespace Opulence\Views\Caching;

interface ICache
{
    /** The default lifetime of a cached template */
    const DEFAULT_LIFETIME = 3600;
    /** The default chance that garbage collection will be run in this instance */
    const DEFAULT_GC_CHANCE = 1;
    /** The default number the chance will be divided by to calculate the probability */
    const DEFAULT_GC_DIVISOR = 1000;

    /**
     * Flushes all of the rendered templates from cache
     */
    public function flush();

    /**
     * Performs garbage collection of expired templates
     */
    public function gc();

    /**
     * Gets the unrendered template with the input data
     *
     * @param string $unrenderedTemplate The unrendered template
     * @param array $variables The variables to match on
     * @param array $tags The tags to match on
     * @return string|null The rendered template if it existed, otherwise null
     */
    public function get($unrenderedTemplate, array $variables = [], array $tags = []);

    /**
     * Gets whether or not the cache has the unrendered template with the input data
     *
     * @param string $unrenderedTemplate The unrendered template
     * @param array $variables The variables to match on
     * @param array $tags The tags to match on
     * @return bool True if the cache has an unexpired rendered template, otherwise false
     */
    public function has($unrenderedTemplate, array $variables = [], array $tags = []);

    /**
     * Stores a rendered template to cache
     *
     * @param string $renderedTemplate The rendered template
     * @param string $unrenderedTemplate The unrendered template
     * @param array $variables The variables to match on
     * @param array $tags The tags to match on
     * @return bool True if successful, otherwise false
     */
    public function set($renderedTemplate, $unrenderedTemplate, array $variables = [], array $tags = []);

    /**
     * Sets the chance that garbage collection will be run
     * For example, passing (123, 1000) means you will have a 123/1000 chance of having to perform garbage collection
     *
     * @param int $chance The chance (out of the total) that garbage collection will be run
     * @param int $divisor The number the chance will be divided by to calculate the probability
     */
    public function setGCChance($chance, $divisor = 100);
}