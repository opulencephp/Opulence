<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Providers;

/**
 * Defines settings for the MySQL provider
 */
class MySqlProvider extends Provider
{
    /** @inheritdoc */
    protected $trueBooleanFormat = 1;
    /** @inheritdoc */
    protected $falseBooleanFormat = 0;
    /** @var string The format for time with time zone strings */
    protected $timeWithTimeZoneFormat = 'H:i:s';
}
