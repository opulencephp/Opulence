<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Boots up our application
 */
use RDev\Models\Applications;
use RDev\Models\Applications\Configs;
use RDev\Models\Applications\Factories;

require_once(__DIR__ . "/../vendor/autoload.php");

// Grab the application config from either direct input or from a file
$applicationConfig = new Configs\ApplicationConfig();
$applicationFactory = new Factories\ApplicationFactory();
$application = $applicationFactory->createFromConfig($applicationConfig);
$application->start();
$application->shutdown();