<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the list of headers
 */
namespace RDev\HTTP;

class Headers extends Parameters
{
    /**
     * @param array $parameters The list of server parameters to create the headers from
     */
    public function __construct(array $parameters = [])
    {
        // Grab all of the server parameters that begin with "HTTP_"
        foreach($parameters as $key => $value)
        {
            if(strpos($key, "HTTP_") === 0)
            {
                $this->set(substr($key, 5), $value);
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
     * {@inheritdoc}
     * @param bool $onlyReturnFirst True if we only want the first header, otherwise we'll return all of them
     */
    public function get($name, $default = null, $onlyReturnFirst = true)
    {
        if($this->has($name))
        {
            $value = $this->parameters[$name];

            if($onlyReturnFirst)
            {
                return $value[0];
            }
        }
        else
        {
            $value = $default;
        }

        return $value;
    }

    /**
     * Headers are allowed to have multiple values, so we must add support for that
     *
     * {@inheritdoc}
     * @param string|array $values The value or values
     * @param bool $shouldReplace Whether or not to replace the value
     */
    public function set($name, $values, $shouldReplace = true)
    {
        if(!is_array($values))
        {
            $values = [$values];
        }

        if($shouldReplace || !$this->has($name))
        {
            $this->parameters[$name] = $values;
        }
        else
        {
            $this->parameters[$name] = array_merge($this->parameters[$name], $values);
        }
    }
} 