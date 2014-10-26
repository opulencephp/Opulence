<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Starts our application
 */
use RDev\Models\Applications;
use RDev\Models\Applications\Configs;
use RDev\Models\Applications\Factories;

require_once(__DIR__ . "/../vendor/autoload.php");

// Grab the application config from either direct input or from a file
$applicationConfig = new Configs\ApplicationConfig([]);
$applicationFactory = new Factories\ApplicationFactory();
$application = $applicationFactory->createFromConfig($applicationConfig);
$application->registerBootstrappers(require_once(__DIR__ . "/bootstrappers.php"));
$application->start();
$application->shutdown();