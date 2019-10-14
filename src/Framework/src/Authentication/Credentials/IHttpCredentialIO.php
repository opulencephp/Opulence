<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Framework\Authentication\Credentials;

use Aphiria\Net\Http\IHttpRequestMessage;
use Aphiria\Net\Http\IHttpResponseMessage;
use Opulence\Authentication\Credentials\ICredential;

/**
 * Defines an interface for HTTP credential IO to implement
 */
interface IHttpCredentialIO
{
    /**
     * Gets the credential from a request
     *
     * @param IHttpRequestMessage $request The request to read from
     * @return ICredential The credential from the request
     */
    public function read(IHttpRequestMessage $request): ICredential;

    /**
     * Removes a credential from the response
     *
     * @param IHttpResponseMessage $response The response to remove from
     */
    public function remove(IHttpResponseMessage $response): void;

    /**
     * Writes a credential to the response
     *
     * @param ICredential $credential The credential to write
     * @param IHttpResponseMessage $response The response to write to
     */
    public function write(ICredential $credential, IHttpResponseMessage $response): void;
}
