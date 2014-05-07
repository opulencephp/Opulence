<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Provides methods for routing a request to the appropriate file/template
 */
namespace RamODev\Application\Shared\Web\Websites;

class Router
{
    /** @var array The various configurations for different HTTP request methods */
    protected $config = array(
        "get" => array(),
        "post" => array(),
        "put" => array(),
        "delete" => array()
    );

    public function get($pattern, $callback, $conditions = array())
    {

    }
} 