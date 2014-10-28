<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines settings for the PostgreSQL provider
 */
namespace RDev\Databases\SQL\Providers;

class PostgreSQL extends Provider
{
    /** {@inheritdoc} */
    protected $timestampWithTimeZoneFormat = "Y-m-d H:i:s O";
    /** @var array The list of acceptable "true" values in PostgreSQL */
    private $trueBooleanValues = [
        't',
        'true',
        '1',
        'y',
        'yes',
        'on'
    ];
    /** @var array The list of acceptable "false" values in PostgreSQL */
    private $falseBooleanValues = [
        'f',
        'false',
        '0',
        'n',
        'no',
        'off'
    ];

    /**
     * {@inheritdoc}
     */
    public function convertFromSQLBoolean($value)
    {
        if(in_array(strtolower($value), $this->falseBooleanValues, true))
        {
            return false;
        }
        elseif(in_array(strtolower($value), $this->trueBooleanValues, true))
        {
            return true;
        }

        return parent::convertFromSQLBoolean($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToSQLBoolean($value)
    {
        if(is_bool($value))
        {
            if($value)
            {
                return $this->trueBooleanValues[0];
            }
            else
            {
                return $this->falseBooleanValues[0];
            }
        }

        return $value;
    }
} 