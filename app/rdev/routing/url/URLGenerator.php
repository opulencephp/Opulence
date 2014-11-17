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
    /** @var Routing\Routes The list of routes */
    private $routes = null;
    /** @var Compilers\ICompiler The compiler to use */
    private $compiler = null;

    /**
     * @param Routing\Routes $routes The list of routes
     * @param Compilers\ICompiler $compiler The compiler ot use
     */
    public function __construct(Routing\Routes &$routes, Compilers\ICompiler $compiler)
    {
        $this->routes = $routes;
        $this->compiler = $compiler;
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
        $route = $this->routes->getNamedRoute($name);

        if($route === null)
        {
            return "";
        }

        if(!is_array($values))
        {
            $values = [$values];
        }

        $this->compiler->compile($route);

        return $this->generateHost($route, $values) . $this->generatePath($route, $values);
    }

    /**
     * Generates the host portion of a URL for a route
     *
     * @param Routing\Route $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated host value
     * @throws URLException Thrown if the generated host is not valid
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

            // Remove any leftover variables
            $generatedHost = preg_replace($this->compiler->getVariableMatchingRegex(), "", $generatedHost);

            // Make sure what we just generated satisfies the regex
            if(!preg_match($route->getHostRegex(), $generatedHost))
            {
                throw new URLException(
                    "Generated host \"$generatedHost\" does not satisfy regex for route \"{$route->getName()}\""
                );
            }

            // Prefix the URL with the protocol
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
     * @throws URLException Thrown if the generated path is not valid
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

        // Remove any leftover variables
        $generatedPath = preg_replace($this->compiler->getVariableMatchingRegex(), "", $generatedPath);

        // Make sure what we just generated satisfies the regex
        if(!preg_match($route->getPathRegex(), $generatedPath))
        {
            throw new URLException(
                "Generated path \"$generatedPath\" does not satisfy regex for route \"{$route->getName()}\""
            );
        }

        return $generatedPath;
    }
}