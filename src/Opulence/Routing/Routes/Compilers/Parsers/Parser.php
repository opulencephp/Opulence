<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Routes\Compilers\Parsers;

use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;

/**
 * Defines the route parser
 */
class Parser implements IParser
{
    /**
     * The maximum supported length of a PCRE subpattern name
     * http://pcre.org/current/doc/html/pcre2pattern.html#SEC16.
     *
     * @internal
     */
    public const VARIABLE_MAXIMUM_LENGTH = 32;
    /** @var string The variable matching regex */
    private static $variableMatchingRegex = '#:([a-zA-Z_][a-zA-Z0-9_]*)(?:=([^:\[\]/]+))?#';
    /** @var int The cursor of the currently parsed route */
    private $cursor = 0;
    /** @var array The list of variable names in the currently parsed route */
    private $variableNames = [];

    /**
     * @inheritdoc
     */
    public function parse(Route $route): ParsedRoute
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
    private function convertRawStringToRegex(ParsedRoute $parsedRoute, string $rawString): string
    {
        if (empty($rawString)) {
            return '#^.*$#';
        }

        $this->variableNames = [];
        $bracketDepth = 0;
        $this->cursor = 0;
        $rawStringLength = mb_strlen($rawString);
        $regex = '';

        while ($this->cursor < $rawStringLength) {
            $char = $rawString[$this->cursor];

            switch ($char) {
                case ':':
                    $regex .= $this->getVarRegex($parsedRoute, mb_substr($rawString, $this->cursor));
                    break;
                case '[':
                    $regex .= '(?:';
                    $bracketDepth++;
                    $this->cursor++;
                    break;
                case ']':
                    $regex .= ')?';
                    $bracketDepth--;
                    $this->cursor++;
                    break;
                default:
                    $regex .= preg_quote($char, '#');
                    $this->cursor++;
            }
        }

        if ($bracketDepth != 0) {
            throw new RouteException(
                sprintf('Route has %s brackets', $bracketDepth > 0 ? 'unclosed' : 'unopened')
            );
        }

        return sprintf('#^%s$#', $regex);
    }

    /**
     * Parses a variable and returns the regex
     *
     * @param ParsedRoute $parsedRoute The route being parsed
     * @param string $segment The segment being parsed
     * @return string The variable regex
     * @throws RouteException Thrown if the variable definition is invalid
     */
    private function getVarRegex(ParsedRoute $parsedRoute, string $segment): string
    {
        if (preg_match(self::$variableMatchingRegex, $segment, $matches) !== 1) {
            throw new RouteException("Variable name can't be empty");
        }

        $variableName = $matches[1];
        $defaultValue = $matches[2] ?? '';

        if (strlen($variableName) > self::VARIABLE_MAXIMUM_LENGTH) {
            throw new RouteException(
                sprintf(
                    'Variable name "%s" cannot be longer than %d characters. Please use a shorter name.',
                    $variableName,
                    self::VARIABLE_MAXIMUM_LENGTH
                )
            );
        }

        if (in_array($variableName, $this->variableNames)) {
            throw new RouteException("Route uses multiple references to \"$variableName\"");
        }

        $this->variableNames[] = $variableName;
        $parsedRoute->setDefaultValue($variableName, $defaultValue);
        $variableRegex = $parsedRoute->getVarRegex($variableName);

        if ($variableRegex === null) {
            // Add a default regex
            $variableRegex = "[^\/:]+";
        }

        $this->cursor += mb_strlen($matches[0]);

        return sprintf('(?P<%s>%s)', $variableName, $variableRegex);
    }
}
