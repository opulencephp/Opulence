<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Tests\Authentication\Credentials\Storage\Mocks;

use Opulence\Authentication\Credentials\ICredential;
use Opulence\Authentication\Credentials\Storage\ICredentialStorage;
use Opulence\Http\Responses\Response;

/**
 *
 */
class CredentialStorage implements ICredentialStorage
{
    /** @var ICredential The list of credentials in storage */
    private $credential = null;
    /** @var string The unhashed token */
    private $unhashedToken = "";

    /**
     * @inheritdoc
     */
    public function delete(Response $response)
    {
        $this->credential = null;
        $this->unhashedToken = "";
    }

    /**
     * @inheritdoc
     */
    public function exists()
    {
        return $this->credential !== null;
    }

    /**
     * @inheritdoc
     */
    public function get()
    {
        return $this->credential;
    }

    /**
     * @inheritdoc
     */
    public function save(Response $response, ICredential $credential, $unhashedToken)
    {
        $this->credential = $credential;
        $this->unhashedToken = $unhashedToken;
    }
} 