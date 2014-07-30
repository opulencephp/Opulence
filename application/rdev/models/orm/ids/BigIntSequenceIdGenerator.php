<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an Id generator that uses a big integer sequence to generate Ids
 */
namespace RDev\Models\ORM\Ids;
use RDev\Models;
use RDev\Models\Databases\SQL;

class BigIntSequenceIdGenerator extends IdGenerator
{
    /** @var string|null The name of the sequence that contains the last insert Id */
    private $sequenceName = null;

    /**
     * @param string|null $sequenceName The name of the sequence that contains the last insert Id
     */
    public function __construct($sequenceName = null)
    {
        $this->sequenceName = $sequenceName;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(Models\IEntity $entity, SQL\IConnection $connection)
    {
        return (string)$connection->lastInsertId($this->sequenceName);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyValue()
    {
        return null;
    }
} 