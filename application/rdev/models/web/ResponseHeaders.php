<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the response headers
 */
namespace RDev\Models\Web;

class ResponseHeaders extends Headers
{
    /**
     * @var array The list of cookie names to their properties
     */
    private $cookies = [];

    /**
     * Deletes a cookie in the response header
     *
     * @param string $name The name of the cookie to delete
     * @param string $path The path the cookie is valid on
     * @param string $domain The domain the cookie is valid on
     */
    public function deleteCookie($name, $path, $domain)
    {
        unset($this->cookies[$domain][$path][$name]);

        // Remove any orphans
        if(isset($this->cookies[$domain][$path]))
        {
            unset($this->cookies[$domain][$path]);

            if(isset($this->cookies[$domain]))
            {
                unset($this->cookies[$domain]);
            }
        }
    }

    /**
     * Gets a list of all the active cookies
     *
     * @return Cookie[] The list of all the set cookies
     */
    public function getCookies()
    {
        $cookies = [];

        foreach($this->cookies as $domain => $cookiesByDomain)
        {
            foreach($cookiesByDomain as $path => $cookiesByPath)
            {
                foreach($cookiesByPath as $name => $cookie)
                {
                    $cookies[] = $cookie;
                }
            }
        }

        return $cookies;
    }

    /**
     * Sets a cookie
     *
     * @param Cookie $cookie The cookie to set
     */
    public function setCookie(Cookie $cookie)
    {
        $this->cookies[$cookie->getDomain()][$cookie->getPath()][$cookie->getName()] = $cookie;
    }
} 