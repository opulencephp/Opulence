<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the PHP bootstrapper
 */
namespace Opulence\Framework\Bootstrappers\PHP;

use Opulence\Applications\Bootstrappers\Bootstrapper;

class PHPBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function initialize()
    {
        // Don't display errors to the browser
        ini_set("display_errors", "off");

        // Force the mbstring extension to be installed
        if (!extension_loaded("mbstring")) {
            die("mbstring extension is required");
        }

        // Force a default timezone
        if (ini_get("date.timezone") === false || date_default_timezone_get() == "UTC") {
            // If there was no default timezone specified, PHP returns "UTC"
            // So, just to be sure that a default timezone is set, set "UTC"
            date_default_timezone_set("UTC");
        }
    }
}