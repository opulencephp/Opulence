<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the list of headers
 */
namespace Opulence\HTTP;

class Headers extends Parameters
{
    /**
     * @param array $parameters The list of server parameters to create the headers from
     */
    public function __construct(array $parameters = [])
    {
        // Grab all of the server parameters that begin with "HTTP_"
        foreach ($parameters as $key => $value) {
            if (mb_strpos($key, "HTTP_") === 0) {
                $this->set(mb_substr($key, 5), $value);
            }elseif (mb_strpos($key, "CONTENT_") === 0) {
                $this->set($key, $value);
            }
        }

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
    public function add($name, $values, $shouldReplace = true)
    {
        $this->set($name, $values, $shouldReplace);
    }

    /**
     * @inheritdoc
     * @param bool $onlyReturnFirst True if we only want the first header, otherwise we'll return all of them
     */
    public function get($name, $default = null, $onlyReturnFirst = true)
    {
        if ($this->has($name)) {
            $value = $this->parameters[$name];

            if ($onlyReturnFirst) {
                return $value[0];
            }
        }else {
            $value = $default;
        }

        return $value;
    }

    /**
     * Headers are allowed to have multiple values, so we must add support for that
     *
     * @inheritdoc
     * @param string|array $values The value or values
     * @param bool $shouldReplace Whether or not to replace the value
     */
    public function set($name, $values, $shouldReplace = true)
    {
        $values = (array)$values;

        if ($shouldReplace || !$this->has($name)) {
            $this->parameters[$name] = $values;
        }else {
            $this->parameters[$name] = array_merge($this->parameters[$name], $values);
        }
    }
} 