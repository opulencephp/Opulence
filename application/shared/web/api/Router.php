<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a RESTful API URL router
 */
namespace RamODev\Application\Shared\Web\API;
use RamODev\Application\Shared\Exceptions\Log;
use RamODev\Application\Shared\Web;

class Router
{
    /**
     * Maps regular expressions to anonymous functions to execute on URL matches
     * The anonymous function must return an API response object
     *
     * @var array
     */
    private $regexesToCallbacks = array();

    /**
     * @param array $regexesToCallbacks Maps regular expressions to anonymous functions to execute on URL matches
     *      The anonymous function must return an API response object
     */
    public function __construct($regexesToCallbacks)
    {
        $this->setRegexesToCallbacks($regexesToCallbacks);
    }

    /**
     * Routes a URL path and gets the API response object
     *
     * @param string $path The URL path to route
     * @return Response An API response object
     */
    public function route($path)
    {
        try
        {
            foreach($this->regexesToCallbacks as $regex => $callback)
            {
                $matches = array();

                if(preg_match("/" . $regex . "/", $path, $matches))
                {
                    // Get rid of the first item in matches, which will be the entire string
                    array_shift($matches);

                    return call_user_func_array($callback, $matches);
                }
            }

            return new Response(Web\Response::HTTP_BAD_REQUEST);
        }
        catch(\Exception $ex)
        {
            Log::write("Failed to route path: " . $ex);
        }

        return new Response(Web\Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param array $regexesToCallbacks
     */
    public function setRegexesToCallbacks($regexesToCallbacks)
    {
        $this->regexesToCallbacks = $regexesToCallbacks;
    }
} 