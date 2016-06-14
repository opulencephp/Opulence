<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials\Storage;

use Opulence\Authentication\Credentials\InvalidCredentialException;
use Opulence\Authentication\Credentials\ICredential;
use Opulence\Http\Responses\Response;

/**
 * Defines the interface for various methods of credential storage to implement
 */
interface ICredentialStorage
{
    /**
     * Deletes the credential from storage
     *
     * @param Response $response The response to delete the credential from
     */
    public function delete(Response $response);

    /**
     * Gets whether or not the credential is set in storage
     *
     * @return bool True if the credential exists in storage, otherwise false
     * @throws InvalidCredentialException Thrown if the credentials exist but are invalid
     */
    public function exists() : bool;

    /**
     * Gets the credential from storage
     *
     * @return ICredential|null The credential from storage if they exist, otherwise null
     * @throws InvalidCredentialException Thrown if the credentials in storage are invalid
     */
    public function get();

    /**
     * Saves the credential to storage
     *
     * @param Response $response The response to save the credential to
     * @param ICredential $credential The credential to save
     * @param string $unhashedToken The unhashed token to save
     */
    public function save(Response $response, ICredential $credential, string $unhashedToken);
} 