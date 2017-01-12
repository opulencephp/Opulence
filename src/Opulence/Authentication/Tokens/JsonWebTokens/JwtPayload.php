<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Authentication\Tokens\JsonWebTokens;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Defines a JWT payload
 */
class JwtPayload
{
    /** @var array The extra claims */
    private $claims = [
        'iss' => null,
        'sub' => null,
        'aud' => null,
        'exp' => null,
        'nbf' => null,
        'iat' => null,
        'jti' => null
    ];
    /** @var string The salt used to make the JTI random */
    private $jtiSalt = '';

    public function __construct()
    {
        $this->jtiSalt = $this->generateJtiSalt();
    }

    /**
     * Base 64 encodes data for use in URLs
     *
     * @param string $data The data to encode
     * @return string The base 64 encoded data that's safe for URLs
     * @link http://php.net/manual/en/function.base64-encode.php#103849
     */
    private static function base64UrlEncode(string $data) : string
    {
        return \rtrim(\strtr(\base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Adds an extra claim
     *
     * @param string $name The name of the claim to add
     * @param mixed $value The value to add
     */
    public function add(string $name, $value)
    {
        if (in_array($name, ['exp', 'nbf', 'iat']) && is_int($value)) {
            $value = DateTimeImmutable::createFromFormat('U', $value);
        }

        $this->claims[$name] = $value;
    }

    /**
     * Gets the header as a base64 URL-encoded string
     *
     * @return string The base64 URL-encoded string
     */
    public function encode() : string
    {
        return self::base64UrlEncode(\json_encode($this->getAll()));
    }

    /**
     * Gets the value for a claim
     *
     * @param string $name The name of the claim to get
     * @return mixed|null The value of the claim if it exists, otherwise null
     */
    public function get(string $name)
    {
        $claims = $this->getAll();

        if (!array_key_exists($name, $claims)) {
            return null;
        }

        return $claims[$name];
    }

    /**
     * Gets the value for all the claims
     *
     * @return array The mapping of set claims to their values
     */
    public function getAll() : array
    {
        $convertedClaims = [];
        $timeFields = ['exp', 'nbf', 'iat'];

        // Convert date times to timestamps
        foreach ($this->claims as $name => $value) {
            if ($value !== null && in_array($name, $timeFields)) {
                /** @var DateTimeImmutable $value */
                $value = $value->getTimestamp();
            }

            $convertedClaims[$name] = $value;
        }

        if (!isset($this->claims['jti'])) {
            $convertedClaims['jti'] = $this->getId();
        }

        return $convertedClaims;
    }

    /**
     * @return array|string|null
     */
    public function getAudience()
    {
        return $this->claims['aud'];
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        if (isset($this->claims['jti'])) {
            return $this->claims['jti'];
        }

        return \md5(\json_encode($this->claims) . $this->jtiSalt);
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getIssuedAt()
    {
        return $this->claims['iat'];
    }

    /**
     * @return string|null
     */
    public function getIssuer()
    {
        return $this->claims['iss'];
    }

    /**
     * @return string|null
     */
    public function getSubject()
    {
        return $this->claims['sub'];
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getValidFrom()
    {
        return $this->claims['nbf'];
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getValidTo()
    {
        return $this->claims['exp'];
    }

    /**
     * @param array|string $audience
     * @throws InvalidArgumentException Thrown if the audience is not the correct type
     */
    public function setAudience($audience)
    {
        if (!is_string($audience) && !is_array($audience)) {
            throw new InvalidArgumentException('Audience must be of type string or array');
        }

        $this->claims['aud'] = $audience;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->claims['jti'] = $id;
    }

    /**
     * @param DateTimeImmutable $issuedAt
     */
    public function setIssuedAt(DateTimeImmutable $issuedAt)
    {
        $this->claims['iat'] = $issuedAt;
    }

    /**
     * @param string $issuer
     */
    public function setIssuer(string $issuer)
    {
        $this->claims['iss'] = $issuer;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject)
    {
        $this->claims['sub'] = $subject;
    }

    /**
     * @param DateTimeImmutable $validFrom
     */
    public function setValidFrom(DateTimeImmutable $validFrom)
    {
        $this->claims['nbf'] = $validFrom;
    }

    /**
     * @param DateTimeImmutable $validTo
     */
    public function setValidTo(DateTimeImmutable $validTo)
    {
        $this->claims['exp'] = $validTo;
    }

    /**
     * Generates a JTI salt to ensure randomness
     *
     * @return string The salt
     */
    private function generateJtiSalt() : string
    {
        return \bin2hex(\random_bytes(8));
    }
}
