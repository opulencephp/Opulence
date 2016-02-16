<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

/**
 * Defines a credential
 */
class Credential implements ICredential
{
    /** @var int The type Id */
    protected $typeId = -1;
    /** @var array The mapping of value names to their values */
    protected $values = [];

    /**
     * @param int $typeId The type Id
     * @param array $values The mapping of value names to their values
     */
    public function __construct(int $typeId, array $values)
    {
        $this->typeId = $typeId;
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function getTypeId() : int
    {
        return $this->typeId;
    }

    /**
     * @inheritdoc
     */
    public function getValue(string $name)
    {
        if (!array_key_exists($name, $this->values)) {
            return null;
        }

        return $this->values[$name];
    }

    /**
     * @inheritdoc
     */
    public function getValues() : array
    {
        return $this->values;
    }
}