<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\Ids\Generators;

use Opulence\Orm\OrmException;

/**
 * Defines an Id generator that uses an integer sequence to generate Ids
 */
class IntSequenceIdGenerator extends SequenceIdGenerator
{
    /**
     * @inheritdoc
     */
    public function generate($entity)
    {
        if ($this->connection === null) {
            throw new OrmException("Connection not set in sequence generator");
        }

        return (int)$this->connection->lastInsertId($this->sequenceName);
    }

    /**
     * @inheritdoc
     */
    public function getEmptyValue($entity)
    {
        return null;
    }
} 