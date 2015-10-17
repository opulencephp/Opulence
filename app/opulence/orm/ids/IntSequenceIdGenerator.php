<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines an Id generator that uses an integer sequence to generate Ids
 */
namespace Opulence\ORM\Ids;

use Opulence\Databases\IConnection;

class IntSequenceIdGenerator extends IdGenerator
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
     * @inheritdoc
     */
    public function generate($entity, IConnection $connection)
    {
        return (int)$connection->lastInsertId($this->sequenceName);
    }

    /**
     * @inheritdoc
     */
    public function getEmptyValue()
    {
        return null;
    }
} 