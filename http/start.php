<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Boots up our application with an HTTP kernel
 */
use RDev\Applications\Application;
use RDev\Framework;
use RDev\HTTP\Requests\Request;

if(!isset($application) || !$application instanceof Application)
{
    die("\$application is not defined");
}

if(!isset($paths) || !$paths instanceof Framework\Paths)
{
    die("\$paths is not defined");
}

$application->getIoCContainer()->bind("RDev\\Framework\\Paths", $paths);
$request = Request::createFromGlobals();
$application->getIoCContainer()->bind("RDev\\HTTP\\Requests\\Request", $request);
$application->registerBootstrappers([
    "RDev\\Framework\\HTTP\\Views\\Bootstrappers\\Template",
    "RDev\\Framework\\HTTP\\Routing\\Bootstrappers\\Router",
    "RDev\\Framework\\HTTP\\Views\\Bootstrappers\\TemplateFunctions",
]);