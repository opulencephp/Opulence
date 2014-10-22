<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for template caches to implement
 */
namespace RDev\Views\Templates;

interface ICache
{
    /**
     * Flushes all of the rendered templates from cache
     */
    public function flush();

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
     * Sets the lifetime of cached templates
     *
     * @param int $lifetime The number of seconds a rendered template should stay cached
     */
    public function setLifetime($lifetime);
}