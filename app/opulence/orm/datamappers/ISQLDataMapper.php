<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for SQL data mappers to implement
 */
namespace Opulence\ORM\DataMappers;

use Opulence\ORM\Ids\IdGenerator;

interface ISQLDataMapper extends IDataMapper
{
    /** Defines a single entity */
    const VALUE_TYPE_ENTITY = 0;
    /** Defines an array of entities */
    const VALUE_TYPE_ARRAY = 1;

    /**
     * Gets the Id generator used by this data mapper
     *
     * @return IdGenerator The Id generator used by this data mapper
     */
    public function getIdGenerator();
} 