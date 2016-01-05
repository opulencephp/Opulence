<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Responses;

use DateTime;

/**
 * Defines a cookie
 */
class Cookie
{
    /** @var string The name of the cookie */
    private $name = "";
    /** @var mixed The value of the cookie */
    private $value = "";
    /** @var int The expiration timestamp of the cookie */
    private $expiration = null;
    /** @var string The path the cookie is valid on */
    private $path = "/";
    /** @var string The domain the cookie is valid on */
    private $domain = "";
    /** @var bool Whether or not this cookie is on HTTPS */
    private $isSecure = false;
    /** @var bool Whether or not this cookie is HTTP only */
    private $isHttpOnly = true;

    /**
     * @param string $name The name of the cookie
     * @param mixed $value The value of the cookie
     * @param DateTime|int $expiration The expiration of the cookie
     * @param string $path The path the cookie is valid on
     * @param string $domain The domain the cookie is valid on
     * @param bool $isSecure Whether or not this cookie is on HTTPS
     * @param bool $isHttpOnly Whether or not this cookie is HTTP only
     */
    public function __construct(
        $name,
        $value,
        $expiration,
        $path = "/",
        $domain = "",
        $isSecure = false,
        $isHttpOnly = true
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->setExpiration($expiration);
        $this->path = $path;
        $this->domain = $domain;
        $this->isSecure = $isSecure;
        $this->isHttpOnly = $isHttpOnly;
    }

    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return int
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isHttpOnly()
    {
        return $this->isHttpOnly;
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->isSecure;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param DateTime|int $expiration
     */
    public function setExpiration($expiration)
    {
        if ($expiration instanceof DateTime) {
            $expiration = $expiration->format("U");
        }

        $this->expiration = $expiration;
    }

    /**
     * @param bool $isHttpOnly
     */
    public function setHttpOnly($isHttpOnly)
    {
        $this->isHttpOnly = $isHttpOnly;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @param bool $isSecure
     */
    public function setSecure($isSecure)
    {
        $this->isSecure = $isSecure;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
} 