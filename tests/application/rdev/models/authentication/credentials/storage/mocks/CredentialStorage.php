<?php
/**
 * Copyright (C) 2014 David Young
 *
 *
 */
namespace RDev\Tests\Models\Authentication\Credentials\Storage\Mocks;
use RDev\Models\Authentication\Credentials;
use RDev\Models\Authentication\Credentials\Storage;
use RDev\Models\HTTP;

class CredentialStorage implements Storage\ICredentialStorage
{
    /** @var Credentials\ICredential The list of credentials in storage */
    private $credential = null;
    /** @var string The unhashed token */
    private $unhashedToken = "";

    /**
     * {@inheritdoc}
     */
    public function delete(HTTP\Response $response)
    {
        $this->credential = null;
        $this->unhashedToken = "";
    }

    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return $this->credential !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->credential;
    }

    /**
     * {@inheritdoc}
     */
    public function save(HTTP\Response $response, Credentials\ICredential $credential, $unhashedToken)
    {
        $this->credential = $credential;
        $this->unhashedToken = $unhashedToken;
    }
} 