<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Databases\Providers;

/**
 * Defines settings for the PostgreSQL provider
 */
class PostgreSqlProvider extends Provider
{
    /** @inheritdoc */
    protected $timestampWithTimeZoneFormat = 'Y-m-d H:i:s O';
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
     * @inheritdoc
     */
    public function convertFromSqlBoolean($value)
    {
        if (in_array(strtolower($value), $this->falseBooleanValues, true)) {
            return false;
        } elseif (in_array(strtolower($value), $this->trueBooleanValues, true)) {
            return true;
        }

        return parent::convertFromSqlBoolean($value);
    }

    /**
     * @inheritdoc
     */
    public function convertToSqlBoolean(bool $value)
    {
        if (is_bool($value)) {
            if ($value) {
                return $this->trueBooleanValues[0];
            } else {
                return $this->falseBooleanValues[0];
            }
        }

        return $value;
    }
}
