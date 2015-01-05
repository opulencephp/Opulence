<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a routing URL generator
 */
namespace RDev\HTTP\Routing\URL;
use RDev\HTTP\Routing\Compilers\Parsers;
use RDev\HTTP\Routing\Routes;

class URLGenerator
{
    /** @var Routes\Routes The list of routes */
    private $routes = null;
    /** @var Parsers\IParser The parser to use */
    private $parser = null;

    /**
     * @param Routes\Routes $routes The list of routes
     * @param Parsers\IParser $parser The parser to use
     */
    public function __construct(Routes\Routes &$routes, Parsers\IParser $parser)
    {
        $this->routes = $routes;
        $this->parser = $parser;
    }

    /**
     * Creates a URL for the named route
     *
     * @param string $name The named of the route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated URL if the route exists, otherwise an empty string
     * @throws URLException Thrown if there was an error generating the URL
     */
    public function createFromName($name, $values = [])
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

        $parsedRoute = $this->parser->parse($route);

        return $this->generateHost($parsedRoute, $values) . $this->generatePath($parsedRoute, $values);
    }

    /**
     * Generates the host portion of a URL for a route
     *
     * @param Routes\ParsedRoute $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated host value
     * @throws URLException Thrown if the generated host is not valid
     */
    private function generateHost(Routes\ParsedRoute $route, &$values)
    {
        $generatedHost = "";
        $variableMatchingRegex = $this->parser->getVariableMatchingRegex();
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
            $generatedHost = preg_replace($variableMatchingRegex, "", $generatedHost);

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
     * @param Routes\ParsedRoute $route The route whose URL we're generating
     * @param mixed|array $values The value or list of values to fill the route with
     * @return string The generated path value
     * @throws URLException Thrown if the generated path is not valid
     */
    private function generatePath(Routes\ParsedRoute $route, &$values)
    {
        $generatedPath = $route->getRawPath();
        $variableMatchingRegex = $this->parser->getVariableMatchingRegex();
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
        $generatedPath = preg_replace($this->parser->getVariableMatchingRegex(), "", $generatedPath);

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