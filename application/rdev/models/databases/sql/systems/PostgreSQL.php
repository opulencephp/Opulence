<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines settings for the PostgreSQL RDBMS
 */
namespace RDev\Models\Databases\SQL\Systems;

class PostgreSQL extends System
{
    /** {@inheritdoc} */
    protected $timestampWithTimeZoneFormat = "Y-m-d H:i:sO";
} 