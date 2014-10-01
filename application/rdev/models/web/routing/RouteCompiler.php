<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the compiler for a route
 */
namespace RDev\Models\Web\Routing;

class RouteCompiler implements IRouteCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Route &$route)
    {
        $pathRegex = $this->quoteStaticText($route->getRawPath());
        $routeVariables = [];
        $matches = [];

        preg_match_all("/\{([^\}]+)\}/", $route->getRawPath(), $matches, PREG_SET_ORDER);

        foreach($matches as $match)
        {
            $variableName = $match[1];
            $defaultValue = "";

            // Set the default value
            if(($equalPos = strpos($match[1], "=")) !== false)
            {
                $variableName = substr($match[1], 0, $equalPos);
                $defaultValue = substr($match[1], $equalPos + 1);
            }

            if(in_array($variableName, $routeVariables))
            {
                throw new \RuntimeException("Route path uses multiple references to \"$variableName\"");
            }

            $routeVariables[] = $variableName;
            $route->setDefaultValue($variableName, $defaultValue);
            $variableRegex = $route->getVariableRegex($variableName);

            if($variableRegex === null)
            {
                // Add a default regex
                $variableRegex = ".+";
            }

            // Insert the regex for this variable back into the path regex
            $pathRegex = str_replace(
                sprintf("{%s}", $match[1]),
                // This gives us the ability to name the match the same as the variable name
                sprintf("(?P<%s>%s)", $variableName, $variableRegex),
                $pathRegex
            );
        }

        $route->setRegex(
            sprintf("/^%s$/", $pathRegex)
        );
    }

    /**
     * Quotes the static text (text not in braces) for use in a regex
     *
     * @param string $path The path to quote
     * @return string The path with the static text quoted
     */
    private function quoteStaticText($path)
    {
        $quotedPath = "";
        $pathLength = strlen($path);
        $braceDepth = 0;
        $quoteBuffer = "";

        for($charIter = 0;$charIter < $pathLength;$charIter++)
        {
            $char = $path[$charIter];

            if($char == "{")
            {
                // Flush out the quote buffer
                if($braceDepth == 0 && strlen($quoteBuffer) > 0)
                {
                    $quotedPath .= preg_quote($quoteBuffer, "/");
                    $quoteBuffer = "";
                }

                $braceDepth++;
            }
            elseif($char == "}")
            {
                $braceDepth--;
            }

            // Make sure that we didn't JUST close all the braces
            if($braceDepth == 0 && $char != "}")
            {
                $quoteBuffer .= $char;
            }
            else
            {
                $quotedPath .= $char;
            }
        }

        // Flush out the buffer
        if(strlen($quoteBuffer) > 0)
        {
            $quotedPath .= preg_quote($quoteBuffer, "/");
        }

        return $quotedPath;
    }
} 