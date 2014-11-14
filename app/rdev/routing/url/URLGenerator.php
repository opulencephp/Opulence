<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines a routing URL generator
 */
namespace RDev\Routing\URL;
use RDev\Routing;
use RDev\Routing\Compilers;

class URLGenerator
{
    /** @var Compilers\ICompiler The compiler to use */
    private $compiler = null;
    /** @var Routing\Route[] The list of named routes */
    private $namedRoutes = [];

    /**
     * @param Compilers\ICompiler $compiler The compiler ot use
     * @param array $namedRoutes The list of named routes
     */
    public function __construct(Compilers\ICompiler $compiler, array &$namedRoutes)
    {
        $this->compiler = $compiler;
        $this->namedRoutes = $namedRoutes;
    }

    /**
     * Generates a URL for the named route
     *
     * @param string $name The named of the route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated URL if the route exists, otherwise an empty string
     * @throws URLException Thrown if there was an error generating the URL
     */
    public function generate($name, $values = [])
    {
        if(!isset($this->namedRoutes[$name]))
        {
            return "";
        }

        if(!is_array($values))
        {
            $values = [$values];
        }

        $route = $this->namedRoutes[$name];
        $url = $this->generateHost($route, $values) . $this->generatePath($route, $values);
        $unfilledMatches = [];

        // Make sure there are no remaining unfilled variables
        if(preg_match($this->compiler->getVariableMatchingRegex(), $url, $unfilledMatches))
        {
            throw new URLException("Unfilled URL variables: " . $unfilledMatches[0]);
        }

        return $url;
    }

    /**
     * Generates the host portion of a URL for a route
     *
     * @param Routing\Route $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated host value
     */
    private function generateHost(Routing\Route $route, &$values)
    {
        $generatedHost = "";
        $variableMatchingRegex = $this->compiler->getVariableMatchingRegex();
        $count = 1000;

        if(!empty($route->getRawHost()))
        {
            $generatedHost = $route->getRawHost();

            while($count > 0 && count($values) > 0)
            {
                $generatedHost = preg_replace($variableMatchingRegex, $values[0], $generatedHost, 1, $count);

                if($count > 0)
                {
                    // Only remove a value if we actually replaced something
                    array_shift($values);
                }
            }

            $generatedHost = "http" . ($route->isSecure() ? "s" : "") . "://" . $generatedHost;
        }

        return $generatedHost;
    }

    /**
     * Generates the path portion of a URL for a route
     *
     * @param Routing\Route $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated path value
     */
    private function generatePath(Routing\Route $route, &$values)
    {
        $generatedPath = $route->getRawPath();
        $variableMatchingRegex = $this->compiler->getVariableMatchingRegex();
        $count = 1000;

        while($count > 0 && count($values) > 0)
        {
            $generatedPath = preg_replace($variableMatchingRegex, $values[0], $generatedPath, 1, $count);

            if($count > 0)
            {
                // Only remove a value if we actually replaced something
                array_shift($values);
            }
        }

        return $generatedPath;
    }
}