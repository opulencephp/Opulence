<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the compiler for a route
 */
namespace RDev\Models\Routing;

class RouteCompiler implements IRouteCompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile(Route &$route)
    {
        $route->setPathRegex($this->convertRawStringToRegex($route, $route->getRawPath()));
        $route->setHostRegex($this->convertRawStringToRegex($route, $route->getRawHost()));
    }

    /**
     * Converts a raw string with path variables to a regex
     *
     * @param Route $route The route whose string we're converting
     * @param string $rawString The raw string to convert
     * @return string The regex
     * @throws RouteException
     */
    private function convertRawStringToRegex(Route &$route, $rawString)
    {
        $regex = $this->quoteStaticText($rawString);
        $routeVariables = [];
        $matches = [];

        preg_match_all("/\{([^\}]+)\}/", $rawString, $matches, PREG_SET_ORDER);

        foreach($matches as $match)
        {
            $variableName = $match[1];
            $defaultValue = "";
            $isOptional = false;

            // Set the default value
            if(($equalPos = strpos($match[1], "=")) !== false)
            {
                $variableName = substr($match[1], 0, $equalPos);
                $defaultValue = substr($match[1], $equalPos + 1);
            }

            // Check if the variable is marked as optional
            if(strpos($variableName, "?") !== false)
            {
                $isOptional = true;
                $variableName = substr($variableName, 0, -1);
            }

            // Check that the variable name is a valid PHP variable name
            // @link http://php.net/manual/en/language.variables.basics.php
            if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $variableName))
            {
                throw new RouteException("Invalid variable name \"$variableName\"");
            }

            if(in_array($variableName, $routeVariables))
            {
                throw new RouteException("Route path uses multiple references to \"$variableName\"");
            }

            $routeVariables[] = $variableName;
            $route->setDefaultValue($variableName, $defaultValue);
            $variableRegex = $route->getVariableRegex($variableName);

            if($variableRegex === null)
            {
                // Add a default regex
                $variableRegex = ".+";
            }

            // Insert the regex for this variable back into the regex
            $regex = str_replace(
                sprintf("{%s}", $match[1]),
                // This gives us the ability to name the match the same as the variable name
                sprintf("(?P<%s>%s)%s", $variableName, $variableRegex, $isOptional ? "?" : ""),
                $regex
            );
        }

        return sprintf("/^%s$/", $regex);
    }

    /**
     * Quotes the static text (text not in braces) for use in a regex
     *
     * @param string $string The string to quote
     * @return string The string with the static text quoted
     * @throws RouteException Thrown if the braces are not nested correctly
     */
    private function quoteStaticText($string)
    {
        $quotedString = "";
        $pathLength = strlen($string);
        $braceDepth = 0;
        $quoteBuffer = "";

        for($charIter = 0;$charIter < $pathLength;$charIter++)
        {
            $char = $string[$charIter];

            if($char == "{")
            {
                // Flush out the quote buffer
                if($braceDepth == 0 && strlen($quoteBuffer) > 0)
                {
                    $quotedString .= preg_quote($quoteBuffer, "/");
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
                $quotedString .= $char;
            }
        }

        // Flush out the buffer
        if(strlen($quoteBuffer) > 0)
        {
            $quotedString .= preg_quote($quoteBuffer, "/");
        }

        if($braceDepth != 0)
        {
            $message = "Route has " . ($braceDepth > 0 ? "unclosed" : "unopened") . " braces";

            throw new RouteException($message);
        }

        return $quotedString;
    }
} 