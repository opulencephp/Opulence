<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines a type mapper that can go back and forth between provider types and PHP types
 */
namespace RDev\Models\Databases\SQL\Providers;

class TypeMapper
{
    /** @var Provider The default provider to use in the case that one isn't specified in a method call */
    protected $provider = null;

    /**
     * @param Provider $provider The default provider to use in the case that one isn't specified in a method call
     */
    public function __construct(Provider $provider = null)
    {
        if(!is_null($provider))
        {
            $this->setProvider($provider);
        }
    }

    /**
     * Converts an SQL boolean to a PHP boolean
     *
     * @param mixed $sqlBoolean The boolean to convert
     * @param Provider $provider The provider to convert from
     * @return bool The PHP boolean
     */
    public function fromSQLBoolean($sqlBoolean, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $provider->getTrueBooleanFormat() === $sqlBoolean;
    }

    /**
     * Converts an SQL date to a PHP date time
     *
     * @param string $sqlDate The date to convert
     * @param Provider $provider The provider to convert from
     * @return \DateTime|null The PHP date
     * @throws \InvalidArgumentException Thrown if the input date couldn't be cast to a PHP date
     */
    public function fromSQLDate($sqlDate, Provider $provider = null)
    {
        if($sqlDate === null)
        {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpDate = \DateTime::createFromFormat($provider->getDateFormat(), $sqlDate, new \DateTimeZone("UTC"));

        if($phpDate === false)
        {
            $phpDate = $this->parseUnknownDateTimeFormat($sqlDate);
        }

        return $phpDate;
    }

    /**
     * Converts an SQL time with time zone to a PHP date time
     *
     * @param string $sqlTime The time to convert
     * @param Provider $provider The provider to convert from
     * @return \DateTime|null The PHP time
     * @throws \InvalidArgumentException Thrown if the input time couldn't be cast to a PHP time
     */
    public function fromSQLTimeWithTimeZone($sqlTime, Provider $provider = null)
    {
        if($sqlTime === null)
        {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpTime = \DateTime::createFromFormat($provider->getTimeWithTimeZoneFormat(), $sqlTime, new \DateTimeZone("UTC"));

        if($phpTime === false)
        {
            $phpTime = $this->parseUnknownDateTimeFormat($sqlTime);
        }

        return $phpTime;
    }

    /**
     * Converts an SQL time without time zone to a PHP date time
     *
     * @param string $sqlTime The time to convert
     * @param Provider $provider The provider to convert from
     * @return \DateTime|null The PHP time
     * @throws \InvalidArgumentException Thrown if the input time couldn't be cast to a PHP time
     */
    public function fromSQLTimeWithoutTimeZone($sqlTime, Provider $provider = null)
    {
        if($sqlTime === null)
        {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpTime = \DateTime::createFromFormat($provider->getTimeWithoutTimeZoneFormat(), $sqlTime, new \DateTimeZone("UTC"));

        if($phpTime === false)
        {
            $phpTime = $this->parseUnknownDateTimeFormat($sqlTime);
        }

        return $phpTime;
    }

    /**
     * Converts an SQL timestamp with time zone to a PHP date time
     *
     * @param string $sqlTimestamp The timestamp with time zone to convert
     * @param Provider $provider The provider to convert from
     * @return \DateTime|null The PHP date time
     * @throws \InvalidArgumentException Thrown if the input timestamp couldn't be cast to a PHP timestamp
     */
    public function fromSQLTimestampWithTimeZone($sqlTimestamp, Provider $provider = null)
    {
        if($sqlTimestamp === null)
        {
            return null;
        }

        $this->setParameterProvider($provider);

        $phpTimestamp = \DateTime::createFromFormat($provider->getTimestampWithTimeZoneFormat(),
            $sqlTimestamp, new \DateTimeZone("UTC"));

        if($phpTimestamp === false)
        {
            $phpTimestamp = $this->parseUnknownDateTimeFormat($sqlTimestamp);
        }

        return $phpTimestamp;
    }

    /**
     * Converts an SQL timestamp without time zone to a PHP date time
     *
     * @param string $sqlTimestamp The timestamp without time zone to convert
     * @param Provider $provider The provider to convert from
     * @return \DateTime|null The PHP date time
     * @throws \InvalidArgumentException Thrown if the input timestamp couldn't be cast to a PHP timestamp
     */
    public function fromSQLTimestampWithoutTimeZone($sqlTimestamp, Provider $provider = null)
    {
        if($sqlTimestamp === null)
        {
            return null;
        }

        $this->setParameterProvider($provider);
        $phpTimestamp = \DateTime::createFromFormat($provider->getTimestampWithoutTimeZoneFormat(), $sqlTimestamp,
            new \DateTimeZone("UTC"));

        if($phpTimestamp === false)
        {
            $phpTimestamp = $this->parseUnknownDateTimeFormat($sqlTimestamp);
        }

        return $phpTimestamp;
    }

    /**
     * @return Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param Provider $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Converts a PHP boolean to an SQL boolean
     *
     * @param bool $boolean The boolean to convert
     * @param Provider $provider The provider to convert to
     * @return mixed The SQL boolean suitable for database storage
     */
    public function toSQLBoolean($boolean, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        if($boolean)
        {
            return $provider->getTrueBooleanFormat();
        }
        else
        {
            return $provider->getFalseBooleanFormat();
        }
    }

    /**
     * Converts a PHP date time to an SQL date
     *
     * @param \DateTime $date The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL date suitable for database storage
     */
    public function toSQLDate(\DateTime $date, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $date->format($provider->getDateFormat());
    }

    /**
     * Converts a PHP date time with time zone to an SQL time
     *
     * @param \DateTime $time The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL time suitable for database storage
     */
    public function toSQLTimeWithTimeZone(\DateTime $time, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $time->format($provider->getTimeWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time without time zone to an SQL time
     *
     * @param \DateTime $time The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL time suitable for database storage
     */
    public function toSQLTimeWithoutTimeZone(\DateTime $time, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $time->format($provider->getTimeWithoutTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp with time zone
     *
     * @param \DateTime $timestamp The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL timestamp with time zone suitable for database storage
     */
    public function toSQLTimestampWithTimeZone(\DateTime $timestamp, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $timestamp->format($provider->getTimestampWithTimeZoneFormat());
    }

    /**
     * Converts a PHP date time to an SQL timestamp without time zone
     *
     * @param \DateTime $timestamp The date time to convert
     * @param Provider $provider The provider to convert to
     * @return string The SQL timestamp without time zone suitable for database storage
     */
    public function toSQLTimestampWithoutTimeZone(\DateTime $timestamp, Provider $provider = null)
    {
        $this->setParameterProvider($provider);

        return $timestamp->format($provider->getTimestampWithoutTimeZoneFormat());
    }

    /**
     * Attempts to parse an unknown date/time format
     *
     * @param string $sqlDateTime The date/time to parse
     * @return \DateTime The PHP date time
     * @throws \InvalidArgumentException Thrown if the input time could not be parsed
     */
    protected function parseUnknownDateTimeFormat($sqlDateTime)
    {
        try
        {
            return new \DateTime($sqlDateTime, new \DateTimeZone("UTC"));
        }
        catch(\Exception $ex)
        {
            throw new \InvalidArgumentException("Unable to cast timestamp: " . $ex->getMessage());
        }
    }

    /**
     * Checks to see that at least the object's provider is set or the input provider is set
     * If the input provider is not set, then it is set by reference to the object's provider
     *
     * @param Provider $provider The provider to set
     * @throws \RuntimeException Thrown if neither the input provider nor the object provider are specified
     */
    protected function setParameterProvider(Provider &$provider = null)
    {
        if(is_null($provider))
        {
            if(is_null($this->provider))
            {
                throw new \RuntimeException("No provider specified");
            }

            $provider = $this->provider;
        }
    }
} 