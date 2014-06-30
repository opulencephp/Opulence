<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base system for RDBMS classes to extend
 */
namespace RDev\Models\Databases\SQL\Systems;

class System
{
    /** @var string The format for date strings */
    protected $dateFormat = "Y-m-d";
    /** @var string The format for time strings */
    protected $timeFormat = "H:i:s";
    /** @var string The format for timestamps with timezones */
    protected $timestampWithTimeZoneFormat = "Y-m-d H:i:s";
    /** @var string The format for timestamps without timezones */
    protected $timestampWithoutTimeZoneFormat = "Y-m-d H:i:s";

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @return string
     */
    public function getTimeFormat()
    {
        return $this->timeFormat;
    }

    /**
     * @return string
     */
    public function getTimestampWithTimeZoneFormat()
    {
        return $this->timestampWithTimeZoneFormat;
    }

    /**
     * @return mixed
     */
    public function getTimestampWithoutTimeZoneFormat()
    {
        return $this->timestampWithoutTimeZoneFormat;
    }
} 