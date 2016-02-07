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
 * Defines a cryptographic token used for security
 */
class Token implements IToken
{
    /** @var int|string The database Id of this token */
    protected $id = -1;
    /** @var int The Id of the user that owns this token */
    protected $userId = -1;
    /** @var string The hashed value */
    protected $hashedValue = "";
    /** @var DateTimeImmutable The valid-from date */
    protected $validFrom = null;
    /** @var DateTimeImmutable The valid-to date */
    protected $validTo = null;
    /** @var bool Whether or not this token is active */
    protected $isActive = false;

    /**
     * @param int|string $id The database Id of this token
     * @param int|string $userId The Id of the user that owns this token
     * @param string $hashedValue The hashed value
     * @param DateTimeImmutable $validFrom The valid-from date
     * @param DateTimeImmutable $validTo The valid-to date
     * @param bool $isActive Whether or not this token is active
     */
    public function __construct(
        $id,
        $userId,
        string $hashedValue,
        DateTimeImmutable $validFrom,
        DateTimeImmutable $validTo,
        bool $isActive
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->hashedValue = $hashedValue;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->isActive = $isActive;
    }

    /**
     * @inheritdoc
     */
    public static function hash(string $unhashedValue) : string
    {
        return hash("sha256", $unhashedValue);
    }

    /**
     * @inheritdoc
     */
    public static function verify(string $hashedValue, string $unhashedValue) : bool
    {
        return hash("sha256", $unhashedValue) === $hashedValue;
    }

    /**
     * @inheritdoc
     */
    public function deactivate()
    {
        $this->isActive = false;
    }

    /**
     * @inheritdoc
     */
    public function getHashedValue() : string
    {
        return $this->hashedValue;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function getValidFrom() : DateTimeImmutable
    {
        return $this->validFrom;
    }

    /**
     * @inheritdoc
     */
    public function getValidTo() : DateTimeImmutable
    {
        return $this->validTo;
    }

    /**
     * @inheritdoc
     */
    public function isActive() : bool
    {
        $now = new DateTimeImmutable();

        return $this->isActive && $this->validFrom <= $now && $now < $this->validTo;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }
} 