<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Boots up our application with a console kernel
 */
use RDev\Applications;
use RDev\Framework;

if(!isset($application) || !$application instanceof Applications\Application)
{
    die("\$application is not defined");
}

if(!isset($paths) || !$paths instanceof Framework\Paths)
{
    die("\$paths is not defined");
}

$application->getIoCContainer()->bind("RDev\\Framework\\Paths", $paths);
$application->registerBootstrappers([
    // Needed so we can flush compiled views via the console
    "RDev\\Framework\\HTTP\\Views\\Bootstrappers\\Template",
    "RDev\\Framework\\Console\\Bootstrappers\\Commands",
    "RDev\\Framework\\Console\\Bootstrappers\\Requests"
]);