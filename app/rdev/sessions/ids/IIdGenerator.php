<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for session Id generators to implement
 */
namespace RDev\Sessions\Ids;

interface IIdGenerator
{
    /**
     * Generates an Id
     *
     * @return string|int The Id
     */
    public function generate();
}