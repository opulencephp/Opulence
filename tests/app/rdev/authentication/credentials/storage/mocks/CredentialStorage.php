<?php
/**
 * Copyright (C) 2015 David Young
 *
 *
 */
namespace RDev\Tests\Authentication\Credentials\Storage\Mocks;
use RDev\Authentication\Credentials;
use RDev\Authentication\Credentials\Storage;
use RDev\HTTP;

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