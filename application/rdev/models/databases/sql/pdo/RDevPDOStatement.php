<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an extension of PDOStatement
 */
namespace RDev\Models\Databases\SQL\PDO;
use RDev\Models\Databases\SQL;

class RDevPDOStatement extends \PDOStatement implements SQL\IStatement
{
    /**
     * We need this because PDO is expecting a private/protected constructor in PDOStatement
     */
    protected function __construct()
    {
        // Don't do anything
    }

    /**
     * Binds a list of values to the statement
     *
     * @param array $values The mapping of parameter name to a value or to an array
     *      If mapping to an array, the first item should be the value and the second should be the data type constant
     * @return bool True if successful, otherwise false
     */
    public function bindValues(array $values)
    {
        $isAssociativeArray = (bool)count(array_filter(array_keys($values), "is_string"));

        foreach($values as $parameterName => $value)
        {
            if(!is_array($value))
            {
                $value = [$value, \PDO::PARAM_STR];
            }

            // If this is an indexed array, we need to offset the parameter name by 1 because it's 1-indexed
            if(!$isAssociativeArray)
            {
                $parameterName += 1;
            }

            if(count($value) != 2 || !$this->bindValue($parameterName, $value[0], $value[1]))
            {
                return false;
            }
        }

        return true;
    }
} 