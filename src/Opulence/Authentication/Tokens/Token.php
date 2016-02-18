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
use InvalidArgumentException;

/**
 * Defines a cryptographic token used for security
 */
class Token implements IToken
{
    /** @var int|string The database Id of this token */
    protected $id = -1;
    /** @var int The Id of the user that owns this token */
    protected $userId = -1;
    /** @var int|string The algorithm used by this token */
    protected $algorithm = Algorithms::SHA256;
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
     * @param int|string $algorithm The algorithm used to hash and verify the value
     * @param string $hashedValue The hashed value
     * @param DateTimeImmutable $validFrom The valid-from date
     * @param DateTimeImmutable $validTo The valid-to date
     * @param bool $isActive Whether or not this token is active
     */
    public function __construct(
        $id,
        $userId,
        $algorithm,
        string $hashedValue,
        DateTimeImmutable $validFrom,
        DateTimeImmutable $validTo,
        bool $isActive
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->setAlgorithm($algorithm);
        $this->hashedValue = $hashedValue;
        $this->validFrom = $validFrom;
        $this->validTo = $validTo;
        $this->isActive = $isActive;
    }

    /**
     * @inheritdoc
     */
    public static function hash($algorithm, string $unhashedValue, array $options = []) : string
    {
        switch ($algorithm) {
            case Algorithms::BCRYPT:
                return password_hash($unhashedValue, PASSWORD_BCRYPT, $options);
            case Algorithms::CRC32:
                return crc32($unhashedValue);
            case Algorithms::MD5:
                return md5($unhashedValue);
            case Algorithms::SHA1:
                return hash("sha1", $unhashedValue);
            case Algorithms::SHA256:
                return hash("sha256", $unhashedValue);
            case Algorithms::SHA512:
                return hash("sha512", $unhashedValue);
            default:
                throw new InvalidArgumentException("Algorithm \"{$algorithm}\" is not supported");
        }
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

    /**
     * @inheritdoc
     */
    public function verify(string $unhashedValue) : bool
    {
        switch ($this->algorithm) {
            case Algorithms::BCRYPT:
                return password_verify($unhashedValue, $this->hashedValue);
            default:
                return self::hash($this->algorithm, $unhashedValue) === $this->hashedValue;
        }
    }

    /**
     * Sets the algorithm
     *
     * @param int|string $algorithm The algorithm
     * @throws InvalidArgumentException Thrown if the input algorithm is not supported
     */
    protected function setAlgorithm($algorithm)
    {
        $supportedAlgorithms = [
            Algorithms::BCRYPT,
            Algorithms::CRC32,
            Algorithms::MD5,
            Algorithms::SHA1,
            Algorithms::SHA256,
            Algorithms::SHA512
        ];

        if (!in_array($algorithm, $supportedAlgorithms)) {
            throw new InvalidArgumentException("Algorithm \"$algorithm\" not supported");
        }

        $this->algorithm = $algorithm;
    }
} 