<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Ids\Generators;

use Opulence\Orm\OrmException;

/**
 * Defines an Id generator that uses a big integer sequence to generate Ids
 */
final class BigIntSequenceIdGenerator extends SequenceIdGenerator
{
    /**
     * @inheritdoc
     */
    public function generate(object $entity)
    {
        if ($this->connection === null) {
            throw new OrmException('Connection not set in sequence generator');
        }

        return (string)$this->connection->lastInsertId($this->sequenceName);
    }

    /**
     * @inheritdoc
     */
    public function getEmptyValue(object $entity)
    {
        return null;
    }
}
