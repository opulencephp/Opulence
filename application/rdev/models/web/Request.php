<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines an HTTP request
 */
namespace RDev\Models\Web;

class Request
{
    /** @var string The client's IP address */
    private $ipAddress = "";
    /** @var string The client's user agent */
    private $userAgent = "";

    public function __construct()
    {
        $this->setIPAddress();
        $this->setUserAgent();
    }

    /**
     * @return string
     */
    public function getIPAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Gets the value of a post variable
     *
     * @param string $name The name of the variable to check
     * @return mixed|bool The post variable if it was set, otherwise false
     */
    public function getPostVar($name)
    {
        if(!$this->isPostVarSet($name))
        {
            return false;
        }

        return $_POST[$name];
    }

    /**
     * Gets the value of a query string variable
     *
     * @param string $name The name of the variable to check
     * @return mixed|bool The query string variable if it was set, otherwise false
     */
    public function getQueryStringVar($name)
    {
        if(!$this->isQueryStringVarSet($name))
        {
            return false;
        }

        return $_GET[$name];
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Gets whether or not a post variable is set to a non-empty value
     *
     * @param string $name The name of the variable to check
     * @return bool True if the post variable has a non-empty value, otherwise false
     */
    public function isPostVarSet($name)
    {
        return isset($_POST[$name]);
    }

    /**
     * Gets whether or not a query string variable is set to a non-empty value
     *
     * @param string $name The name of the variable to check
     * @return bool True if the query string variable has a non-empty value, otherwise false
     */
    public function isQueryStringVarSet($name)
    {
        return isset($_GET[$name]);
    }

    /**
     * Sets the IP address attribute
     */
    private function setIPAddress()
    {
        $ipKeys = ["HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP",
            "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR"];

        foreach($ipKeys as $key)
        {
            if(array_key_exists($key, $_SERVER))
            {
                foreach(explode(",", $_SERVER[$key]) as $ipAddress)
                {
                    $ipAddress = trim($ipAddress);

                    if(filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE
                            | FILTER_FLAG_NO_RES_RANGE) !== false
                    )
                    {
                        $this->ipAddress = $ipAddress;

                        return;
                    }
                }
            }
        }

        $this->ipAddress = isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "";
    }

    /**
     * Sets the user agent attribute
     */
    private function setUserAgent()
    {
        $this->userAgent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : "";
    }
} 