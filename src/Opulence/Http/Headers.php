<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Http;

/**
 * Defines the list of headers
 */
class Headers extends Collection
{
    /** @var array The list of HTTP request headers that don't begin with "HTTP_" */
    protected static $specialCaseHeaders = [
        'AUTH_TYPE' => true,
        'CONTENT_LENGTH' => true,
        'CONTENT_TYPE' => true,
        'PHP_AUTH_DIGEST' => true,
        'PHP_AUTH_PW' => true,
        'PHP_AUTH_TYPE' => true,
        'PHP_AUTH_USER' => true
    ];

    public function __construct()
    {
        /**
         * Headers allow multiple values
         * The parent class does not have this feature, which is why we took care of it in this constructor
         * To satisfy the parent constructor, we'll simply send it an empty array
         */
        parent::__construct([]);
    }

    /**
     * Headers are allowed to have multiple values, so we must add support for that
     *
     * @inheritdoc
     * @param string|array $values The value or values
     * @param bool $shouldReplace Whether or not to replace the value
     */
    public function add(string $name, $values, bool $shouldReplace = true)
    {
        $this->set($name, $values, $shouldReplace);
    }

    /**
     * @inheritdoc
     * @param bool $onlyReturnFirst True if we only want the first header, otherwise we'll return all of them
     */
    public function get(string $name, $default = null, bool $onlyReturnFirst = true)
    {
        if ($this->has($name)) {
            $value = $this->values[$this->normalizeName($name)];

            if ($onlyReturnFirst) {
                return $value[0];
            }
        } else {
            $value = $default;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function has(string $name) : bool
    {
        return parent::has($this->normalizeName($name));
    }

    /**
     * @inheritdoc
     */
    public function remove(string $name)
    {
        parent::remove($this->normalizeName($name));
    }

    /**
     * Headers are allowed to have multiple values, so we must add support for that
     *
     * @inheritdoc
     * @param string|array $values The value or values
     * @param bool $shouldReplace Whether or not to replace the value
     */
    public function set(string $name, $values, bool $shouldReplace = true)
    {
        $name = $this->normalizeName($name);
        $values = (array)$values;

        if ($shouldReplace || !$this->has($name)) {
            parent::set($name, $values);
        } else {
            parent::set($name, array_merge($this->values[$name], $values));
        }
    }

    /**
     * Normalizes a name
     *
     * @param string $name The name to normalize
     * @return string The normalized name
     */
    protected function normalizeName(string $name) : string
    {
        return strtr(strtolower($name), '_', '-');
    }
}
