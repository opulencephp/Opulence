<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the parser for a route
 */
namespace RDev\Routing\Routes\Compilers\Parsers;
use RDev\Routing\Routes\ParsedRoute;
use RDev\Routing\Routes\Route;
use RDev\Routing\RouteException;

class Parser implements IParser
{
    /**
     * {@inheritdoc}
     */
    public function getVariableMatchingRegex()
    {
        return "/(\{([^\}]+)\})/";
    }

    /**
     * {@inheritdoc}
     */
    public function parse(Route $route)
    {
        $parsedRoute = new ParsedRoute($route);
        $parsedRoute->setPathRegex($this->convertRawStringToRegex($parsedRoute, $parsedRoute->getRawPath()));
        $parsedRoute->setHostRegex($this->convertRawStringToRegex($parsedRoute, $parsedRoute->getRawHost()));

        return $parsedRoute;
    }

    /**
     * Converts a raw string with variables to a regex
     *
     * @param ParsedRoute $parsedRoute The route whose string we're converting
     * @param string $rawString The raw string to convert
     * @return string The regex
     * @throws RouteException Thrown if the route variables are not correctly defined
     */
    private function convertRawStringToRegex(ParsedRoute &$parsedRoute, $rawString)
    {
        if(empty($rawString))
        {
            return "#^.*$#";
        }

        $regex = $this->quoteStaticText($rawString);
        $routeVariables = [];
        $matches = [];

        preg_match_all($this->getVariableMatchingRegex(), $rawString, $matches, PREG_SET_ORDER);

        foreach($matches as $match)
        {
            $variableName = $match[2];
            $defaultValue = "";
            $isOptional = false;

            // Set the default value
            if(($equalPos = mb_strpos($match[2], "=")) !== false)
            {
                $variableName = mb_substr($match[2], 0, $equalPos);
                $defaultValue = mb_substr($match[2], $equalPos + 1);
            }

            // Check if the variable is marked as optional
            if(mb_strpos($variableName, "?") !== false)
            {
                $isOptional = true;
                $variableName = mb_substr($variableName, 0, -1);
            }

            // Check that the variable name is a valid PHP variable name
            // @link http://php.net/manual/en/language.variables.basics.php
            if(!preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/", $variableName))
            {
                throw new RouteException("Invalid variable name \"$variableName\"");
            }

            if(in_array($variableName, $routeVariables))
            {
                throw new RouteException("Route uses multiple references to \"$variableName\"");
            }

            $routeVariables[] = $variableName;
            $parsedRoute->setDefaultValue($variableName, $defaultValue);
            $variableRegex = $parsedRoute->getVariableRegex($variableName);

            if($variableRegex === null)
            {
                // Add a default regex
                $variableRegex = "[^\/]+";
            }

            // Insert the regex for this variable back into the regex
            $regex = str_replace(
                sprintf("{%s}", $match[2]),
                // This gives us the ability to name the match the same as the variable name
                sprintf("(?P<%s>%s)%s", $variableName, $variableRegex, $isOptional ? "?" : ""),
                $regex
            );
        }

        return sprintf("#^%s$#", $regex);
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
        $stringLength = mb_strlen($string);
        $braceDepth = 0;
        $quoteBuffer = "";

        for($charIter = 0;$charIter < $stringLength;$charIter++)
        {
            $char = $string[$charIter];

            if($char == "{")
            {
                // Flush out the quote buffer
                if($braceDepth == 0 && mb_strlen($quoteBuffer) > 0)
                {
                    $quotedString .= preg_quote($quoteBuffer, "#");
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
        if(mb_strlen($quoteBuffer) > 0)
        {
            $quotedString .= preg_quote($quoteBuffer, "#");
        }

        if($braceDepth != 0)
        {
            $message = "Route has " . ($braceDepth > 0 ? "unclosed" : "unopened") . " braces";

            throw new RouteException($message);
        }

        return $quotedString;
    }
} 