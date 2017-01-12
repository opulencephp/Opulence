<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Credentials;

/**
 * Defines a credential
 */
class Credential implements ICredential
{
    /** @var string The type of credential this is */
    protected $type = -1;
    /** @var array The mapping of value names to their values */
    protected $values = [];

    /**
     * @param string $type The type of credential this is
     * @param array $values The mapping of value names to their values
     */
    public function __construct(string $type, array $values)
    {
        $this->type = $type;
        $this->values = $values;
    }

    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return $this->type;
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
