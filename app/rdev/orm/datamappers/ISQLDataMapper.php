<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for SQL data mappers to implement
 */
namespace RDev\ORM\DataMappers;
use RDev\ORM\Ids;

interface ISQLDataMapper extends IDataMapper
{
    /**
     * Gets the Id generator used by this data mapper
     *
     * @return Ids\IdGenerator The Id generator used by this data mapper
     */
    public function getIdGenerator();
} 