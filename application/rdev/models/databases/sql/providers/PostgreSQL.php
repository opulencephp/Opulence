<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines settings for the PostgreSQL provider
 */
namespace RDev\Models\Databases\SQL\Providers;

class PostgreSQL extends Provider
{
    /** {@inheritdoc} */
    protected $timestampWithTimeZoneFormat = "Y-m-d H:i:s.uP";
} 