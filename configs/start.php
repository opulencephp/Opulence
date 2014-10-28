<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Starts our application
 */
use RDev\Applications;
use RDev\Applications\Configs;
use RDev\Applications\Factories;

require_once(__DIR__ . "/../vendor/autoload.php");

// Grab the application config from either direct input or from a file
$configArray = [
    "bootstrappers" => [
        "RDev\\IoC\\Bootstrappers\\Container"
    ]
];
$applicationConfig = new Configs\ApplicationConfig($configArray);
$applicationFactory = new Factories\ApplicationFactory();
$application = $applicationFactory->createFromConfig($applicationConfig);
$application->start();
$application->shutdown();