<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Sessions\Ids\Generators;

/**
 * Defines the interface for session Id generators to implement
 */
interface IIdGenerator
{
    /** The minimum length Id that is cryptographically secure */
    const MIN_LENGTH = 16;
    /** The maximum length Id that PHP allows */
    const MAX_LENGTH = 128;

    /**
     * Generates an Id
     *
     * @return string|int The Id
     */
    public function generate();

    /**
     * Gets whether or not an Id is valid
     *
     * @param mixed $id The Id to validate
     * @return bool True if the Id is valid, otherwise false
     */
    public function idIsValid($id) : bool;
}