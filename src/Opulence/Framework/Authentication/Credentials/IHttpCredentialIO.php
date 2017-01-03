<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Authentication\Credentials;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Defines an interface for HTTP credential IO to implement
 */
interface IHttpCredentialIO
{
    /**
     * Gets the credential from a request
     * 
     * @param Request $request The request to read from
     * @return ICredential The credential from the request
     */
    public function read(Request $request) : ICredential;
    
    /**
     * Removes a credential from the response
     * 
     * @param Response $response The response to remove from
     */
    public function remove(Response $response);

    /**
     * Writes a credential to the response
     * 
     * @param ICredential $credential The credential to write
     * @param Response $response The response to write to
     */
    public function write(ICredential $credential, Response $response);
}