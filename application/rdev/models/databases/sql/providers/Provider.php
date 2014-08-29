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
    /** @var string The format for time with time zone strings */
    protected $timeWithTimeZoneFormat = "H:i:sO";
    /** @var string The format for time without time zone strings */
    protected $timeWithoutTimeZoneFormat = "H:i:s";
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
    public function getTimeWithTimeZoneFormat()
    {
        return $this->timeWithTimeZoneFormat;
    }

    /**
     * @return string
     */
    public function getTimeWithoutTimeZoneFormat()
    {
        return $this->timeWithoutTimeZoneFormat;
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