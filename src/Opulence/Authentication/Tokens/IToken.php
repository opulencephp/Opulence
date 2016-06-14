<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Tokens;

use DateTimeImmutable;

/**
 * Defines the interface for cryptographic tokens
 */
interface IToken
{
    /**
     * Marks this token is inactive
     */
    public function deactivate();

    /**
     * Gets the hashed value of this token
     *
     * @return string The hashed value
     */
    public function getHashedValue() : string;

    /**
     * Gets the database Id
     *
     * @return int|string The database Id
     */
    public function getId();

    /**
     * Gets the user Id
     *
     * @return int|string The user Id
     */
    public function getUserId();

    /**
     * Gets the valid-from date of this token
     *
     * @return DateTimeImmutable The valid-from date
     */
    public function getValidFrom() : DateTimeImmutable;

    /**
     * Gets the valid-to date of this token
     *
     * @return DateTimeImmutable The valid-to date
     */
    public function getValidTo() : DateTimeImmutable;

    /**
     * Gets whether or not this token is active
     *
     * @return bool True if the token is active, otherwise false
     */
    public function isActive() : bool;

    /**
     * Sets the database Id of the token
     *
     * @param int|string $id The database Id
     */
    public function setId($id);
}