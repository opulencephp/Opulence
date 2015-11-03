<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http\Responses;

/**
 * Defines a redirect response
 */
class RedirectResponse extends Response
{
    /** @var string The target URL */
    protected $targetUrl = "";

    /**
     * @param string $targetUrl The URL to redirect to
     * @param int $statusCode The HTTP status code
     * @param array $headers The headers to set
     */
    public function __construct($targetUrl, $statusCode = ResponseHeaders::HTTP_FOUND, array $headers = [])
    {
        parent::__construct("", $statusCode, $headers);

        $this->setTargetUrl($targetUrl);
    }

    /**
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }

    /**
     * @param string $targetUrl
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
        $this->headers->set("Location", $this->targetUrl);
    }
} 