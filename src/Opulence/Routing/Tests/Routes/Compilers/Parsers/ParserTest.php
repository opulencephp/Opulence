<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Routes\Compilers\Parsers;

use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;

/**
 * Tests the route parser
 */
class ParserTest extends \PHPUnit\Framework\TestCase
{
    /** @var Parser The parser to use in tests */
    private $parser;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->parser = new Parser();
    }

    /**
     * Tests not specifying a host
     */
    public function testNotSpecifyingHost(): void
    {
        $route = new Route(['get'], '/foo', 'foo@bar');
        $parsedRoute = $this->parser->parse($route);
        $this->assertEquals('#^.*$#', $parsedRoute->getHostRegex());
    }

    /**
     * Tests an optional slash and variable
     */
    public function testOptionalSlashAndVariable(): void
    {
        $rawString = '/:foo/bar[/:blah]';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>[^\/:]+)" . preg_quote('/bar', '#') . "(?:/(?P<blah>[^\/:]+))?"
                )
            )
        );
    }

    /**
     * Tests an optional variable
     */
    public function testOptionalVariable(): void
    {
        $rawString = '/:foo/bar/[:blah]';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>[^\/:]+)" . preg_quote('/bar/', '#') . "(?:(?P<blah>[^\/:]+))?"
                )
            )
        );
    }

    /**
     * Tests an optional variable with a default value
     */
    public function testOptionalVariableWithDefaultValue(): void
    {
        $rawString = '/:foo/bar/[:blah=123]';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>[^\/:]+)" . preg_quote('/bar/', '#') . "(?:(?P<blah>[^\/:]+))?"
                )
            )
        );
        $this->assertEquals('123', $parsedRoute->getDefaultValue('blah'));
    }

    /**
     * Tests parsing a path with multiple variables
     */
    public function testParsingMultipleVariables(): void
    {
        $rawString = '/:foo/bar/:blah';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>[^\/:]+)" . preg_quote('/bar/', '#') . "(?P<blah>[^\/:]+)"
                )
            )
        );
    }

    /**
     * Tests parsing a path with multiple variables with regexes
     */
    public function testParsingMultipleVariablesWithRegexes(): void
    {
        $rawString = '/:foo/bar/:blah';
        $options = [
            'vars' => [
                'foo' => "\d+",
                'blah' => '[a-z]{3}'
            ],
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>\d+)" . preg_quote('/bar/', '#') . '(?P<blah>[a-z]{3})'
                )
            )
        );
    }

    /**
     * Tests parsing a path with a single variable
     */
    public function testParsingSingleVariable(): void
    {
        $rawString = '/:foo';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>[^\/:]+)"
                )
            )
        );
    }

    /**
     * Tests parsing a path with a single variable
     */
    public function testParsingSingleVariableWithFirstDigit(): void
    {
        $this->expectException(RouteException::class);
        $route = new Route(['get'], '/:0foo', 'foo@bar');
        $this->parser->parse($route);
    }

    /**
     * Tests parsing with too long a variable name
     */
    public function testParsingWithTooLongVariableName(): void
    {
        $this->expectException(RouteException::class);
        $route = new Route(['get'], '/:' . str_repeat('a', Parser::VARIABLE_MAXIMUM_LENGTH + 1), 'foo@bar');
        $this->parser->parse($route);
    }

    /**
     * Tests parsing a path with a single variable with a default value
     */
    public function testParsingSingleVariableWithDefaultValue(): void
    {
        $rawString = '/:foo=23';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>[^\/:]+)"
                )
            )
        );
        $this->assertEquals('23', $parsedRoute->getDefaultValue('foo'));
    }

    /**
     * Tests parsing a path with a single variable with options
     */
    public function testParsingSingleVariableWithRegexes(): void
    {
        $rawString = '/:foo';
        $options = [
            'vars' => ['foo' => "\d+"],
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote('/', '#') . "(?P<foo>\d+)"
                )
            )
        );
    }

    /**
     * Tests parsing a static path
     */
    public function testParsingStaticPath(): void
    {
        $rawString = '/foo/bar/blah';
        $options = [
            'host' => $rawString
        ];
        $route = new Route(['get'], $rawString, 'foo@bar', $options);
        $parsedRoute = $this->parser->parse($route);
        $this->assertTrue(
            $this->regexesMach(
                $parsedRoute,
                sprintf(
                    '#^%s$#',
                    preg_quote($rawString, '#')
                )
            )
        );
    }

    /**
     * Tests parsing a path with duplicate variables
     */
    public function testParsingWithDuplicateVariables(): void
    {
        $this->expectException(RouteException::class);
        $route = new Route(['get'], '/:foo/:foo', 'foo@bar');
        $this->parser->parse($route);
    }

    /**
     * Tests parsing a path with empty variable
     */
    public function testParsingWithEmptyVariable(): void
    {
        $this->expectException(RouteException::class);
        $route = new Route(['get'], '/:/bar', 'foo@bar');
        $this->parser->parse($route);
    }

    /**
     * Tests parsing a path with an unclosed open bracket
     */
    public function testParsingWithUnclosedOpenBracket(): void
    {
        $this->expectException(RouteException::class);
        $route = new Route(['get'], '/:foo/[bar', 'foo@bar');
        $this->parser->parse($route);
    }

    /**
     * Tests parsing a path with an unopened close bracket
     */
    public function testParsingWithUnopenedCloseBracket(): void
    {
        $this->expectException(RouteException::class);
        $route = new Route(['get'], '/:foo/:bar]', 'foo@bar');
        $this->parser->parse($route);
    }

    /**
     * Tests specifying an empty path
     */
    public function testSpecifyingEmptyPath(): void
    {
        $route = new Route(['get'], '', 'foo@bar');
        $parsedRoute = $this->parser->parse($route);
        $this->assertEquals('#^.*$#', $parsedRoute->getPathRegex());
    }

    /**
     * Gets whether or not a route's regexes match the input regex
     *
     * @param ParsedRoute $route The route whose regexes we're matching
     * @param string $regex The expected regex
     * @return bool True if the regexes match, otherwise false
     */
    private function regexesMach(ParsedRoute $route, $regex): bool
    {
        return $route->getPathRegex() == $regex && $route->getHostRegex() == $regex;
    }
}
