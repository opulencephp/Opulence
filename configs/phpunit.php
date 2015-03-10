<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Sets up PHPUnit tests
 */
require_once __DIR__ . "/../vendor/autoload.php";

// Turn on error reporting
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");

// Set the default timezone in case the test server doesn't have it already set
date_default_timezone_set("UTC");