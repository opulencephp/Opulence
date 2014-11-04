<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a route that is dispatched when the router misses on a path
 */
namespace RDev\Routing;
use RDev\HTTP;

class MissingRoute extends Route
{
    /** @var array The list of methods this route handles */
    private static $methods = ["GET", "POST", "PUT", "DELETE"];

    /**
     * @param string $controllerClass The name of the controller to call
     */
    public function __construct($controllerClass)
    {
        parent::__construct(self::$methods, "", ["controller" => $controllerClass . "@showHTTPError"]);

        $this->setDefaultValue("statusCode", HTTP\ResponseHeaders::HTTP_NOT_FOUND);
    }
}