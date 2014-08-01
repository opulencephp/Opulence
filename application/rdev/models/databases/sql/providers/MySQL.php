<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines settings for the MySQL RDBMS
 */
namespace RDev\Models\Databases\SQL\Providers;

class MySQL extends Provider
{
    /** {@inheritdoc} */
    protected $trueBooleanFormat = 1;
    /** {@inheritdoc} */
    protected $falseBooleanFormat = 0;
} 