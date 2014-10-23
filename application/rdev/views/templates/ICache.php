<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for template caches to implement
 */
namespace RDev\Views\Templates;

interface ICache
{
    /** The default lifetime of a cached template */
    const DEFAULT_LIFETIME = 3600;
    /** The default chance that garbage collection will be run in this instance */
    const DEFAULT_GC_CHANCE = 1;
    /** The default number the chance will be divided by to calculate the probability */
    const DEFAULT_GC_TOTAL = 1000;

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
     * Gets the lifetime of a rendered template
     *
     * @return int The number of seconds a rendered template stays in cache
     */
    public function getLifetime();

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
     * @param int $total The number the chance will be divided by to calculate the probability
     */
    public function setGCChance($chance, $total = 100);

    /**
     * Sets the lifetime of cached templates
     *
     * @param int $lifetime The number of seconds a rendered template should stay cached
     */
    public function setLifetime($lifetime);
}