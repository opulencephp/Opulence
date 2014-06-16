<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the statement class for use in testing
 */
namespace RDev\Tests\Models\Databases\SQL\Mocks;
use RDev\Models\Databases\SQL;

class Statement implements SQL\IStatement
{
    /**
     * {@inheritdoc}
     */
    public function bindParam($parameter, &$variable, $dataType = \PDO::PARAM_STR)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bindValues(array $values)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function execute($parameters = null)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        return 0;
    }
} 