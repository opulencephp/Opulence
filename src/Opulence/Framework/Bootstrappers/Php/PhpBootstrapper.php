<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Bootstrappers\Php;

use Opulence\Bootstrappers\Bootstrapper;

/**
 * Defines the PHP bootstrapper
 */
class PhpBootstrapper extends Bootstrapper
{
    /**
     * @inheritdoc
     */
    public function initialize()
    {
        // Force a default timezone
        if (ini_get("date.timezone") === false || date_default_timezone_get() == "UTC") {
            // If there was no default timezone specified, PHP returns "UTC"
            // So, just to be sure that a default timezone is set, set "UTC"
            date_default_timezone_set("UTC");
        }
    }
}