<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an exception that is thrown when a query builder detects an invalid query
 */
namespace RamODev\Databases\RDBMS\QueryBuilders\Exceptions;
use RamODev\Exceptions;

require_once(__DIR__ . "/../../../../exceptions/Exception.php");

class InvalidQueryException extends Exceptions\Exception
{
    // Do nothing
} 