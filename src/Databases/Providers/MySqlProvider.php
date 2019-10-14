<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Databases\Providers;

/**
 * Defines settings for the MySQL provider
 */
final class MySqlProvider extends Provider
{
    /** @var int The format for a true boolean value */
    protected int $trueBooleanFormat = 1;
    /** @var int The format for a false boolean value */
    protected int $falseBooleanFormat = 0;
    /** @var string The format for time with time zone strings */
    protected string $timeWithTimeZoneFormat = 'H:i:s';
}
