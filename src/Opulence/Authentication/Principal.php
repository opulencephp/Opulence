<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Defines a principal identity
 */
class Principal implements IPrincipal
{
    /** @var string The type of principal this is */
    protected $type = "";
    /** @var mixed|null The identity of the principal */
    protected $identity = null;

    /**
     * @param string $type The type of principal this is
     * @param mixed $identity The identity of the principal
     */
    public function __construct(string $type, $identity)
    {
        $this->type = $type;
        $this->identity = $identity;
    }

    /**
     * @inheritdoc
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return $this->type;
    }
}