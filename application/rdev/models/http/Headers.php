<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the list of headers
 */
namespace RDev\Models\HTTP;

class Headers extends Parameters
{
    /**
     * @param array $parameters The list of server parameters to create the headers from
     */
    public function __construct(array $parameters = [])
    {
        $headerParameters = [];

        // Grab all of the server parameters that begin with "HTTP_"
        foreach($parameters as $key => $value)
        {
            if(strpos($key, "HTTP_") === 0)
            {
                $headerParameters[substr($key, 5)] = $value;
            }
        }

        parent::__construct($headerParameters);
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