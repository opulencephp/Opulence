<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the base provider for RDBMS providers to extend
 */
namespace RDev\Models\Databases\SQL\Providers;

class Provider
{
    /** @var string The format for a true boolean */
    protected $trueBooleanFormat = "t";
    /** @var string The format for a false boolean */
    protected $falseBooleanFormat = "f";
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
    public function getFalseBooleanFormat()
    {
        return $this->falseBooleanFormat;
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
     * @return string
     */
    public function getTimestampWithoutTimeZoneFormat()
    {
        return $this->timestampWithoutTimeZoneFormat;
    }

    /**
     * @return string
     */
    public function getTrueBooleanFormat()
    {
        return $this->trueBooleanFormat;
    }
} 