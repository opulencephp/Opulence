<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\Ids\Generators;

use Opulence\Databases\IConnection;

/**
 * Defines a sequence Id generator
 */
abstract class SequenceIdGenerator implements IIdGenerator
{
    /** @var IConnection The connection to use */
    protected $connection = null;
    /** @var string The name of the sequence */
    protected $sequenceName = "";

    /**
     * @param string $sequenceName The name of the sequence
     * @param IConnection|null $connection The connection to use
     */
    public function __construct(string $sequenceName, IConnection $connection = null)
    {
        $this->sequenceName = $sequenceName;

        if ($connection !== null) {
            $this->setConnection($connection);
        }
    }

    /**
     * @inheritdoc
     */
    public function isPostInsert() : bool
    {
        return true;
    }

    /**
     * Sets the connection to use to generate the sequence Id
     *
     * @param IConnection $connection The connection to use
     */
    public function setConnection(IConnection $connection)
    {
        $this->connection = $connection;
    }
}