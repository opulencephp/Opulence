<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines settings for the MySQL provider
 */
namespace Opulence\Databases\Providers;

class MySQLProvider extends Provider
{
    /** {@inheritdoc} */
    protected $trueBooleanFormat = 1;
    /** {@inheritdoc} */
    protected $falseBooleanFormat = 0;
    /** @var string The format for time with time zone strings */
    protected $timeWithTimeZoneFormat = "H:i:s";
} 