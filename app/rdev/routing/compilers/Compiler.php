<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Defines a route compiler
 */
namespace RDev\Routing\Compilers;
use RDev\HTTP;
use RDev\Routing\Routes;

class Compiler implements ICompiler
{
    /** @var Parsers\IParser The route parser */
    private $parser = null;

    /**
     * @param Parsers\IParser $parser The route parser
     */
    public function __construct(Parsers\IParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(Routes\Route $route, HTTP\Request $request)
    {
        $parsedRoute = $this->parser->parse($route);
        $hostMatches = [];
        $pathMatches = [];

        if(
            (($route->isSecure() && $request->isSecure()) || !$route->isSecure()) &&
            preg_match($parsedRoute->getHostRegex(), $request->getHeaders()->get("HOST"), $hostMatches) &&
            preg_match($parsedRoute->getPathRegex(), $request->getPath(), $pathMatches)
        )
        {
            // Remove the subjects
            array_shift($hostMatches);
            array_shift($pathMatches);
            $pathVariables = array_merge($hostMatches, $pathMatches);

            return new Routes\CompiledRoute($parsedRoute, true, $pathVariables);
        }
        else
        {
            return new Routes\CompiledRoute($parsedRoute, false);
        }
    }
}