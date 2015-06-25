<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a host
 */
namespace RDev\Applications\Environments\Hosts;

class Host
{
    /** @var string The host */
    private $host = "";
    /** @var bool Whether or not this host uses a regex */
    private $usesRegex = false;

    /**
     * @param string $host The host
     * @param bool $usesRegex Whether or not the host uses a regex
     */
    public function __construct($host, $usesRegex)
    {
        $this->host = $host;
        $this->usesRegex = $usesRegex;
    }

    /**
     * Gets the host
     *
     * @return string The host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Gets whether or not the host uses a regex to match
     *
     * @return bool True if the host uses a regex, otherwise false
     */
    public function usesRegex()
    {
        return $this->usesRegex;
    }
}