<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the PDO statement for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\PDO\Mocks;
use RDev\Models\Databases\SQL\PDO;

class Statement extends PDO\Statement
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     * We have to mock this because attempting to bind a value to an unopened connection will always fail
     */
    public function bindValues(array $values)
    {
        foreach($values as $parameterName => $value)
        {
            if(!is_array($value))
            {
                $value = [$value, \PDO::PARAM_STR];
            }

            // Here we don't actually attempt to bind the value
            if(count($value) != 2)
            {
                return false;
            }
        }

        return true;
    }
} 