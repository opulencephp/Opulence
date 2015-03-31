<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for SQL data mappers to implement
 */
namespace RDev\ORM\DataMappers;
use RDev\ORM\Ids\IdGenerator;

interface ISQLDataMapper extends IDataMapper
{
    /**
     * Gets the Id generator used by this data mapper
     *
     * @return IdGenerator The Id generator used by this data mapper
     */
    public function getIdGenerator();
} 