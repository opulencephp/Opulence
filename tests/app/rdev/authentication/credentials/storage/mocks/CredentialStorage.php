<?php
/**
 * Copyright (C) 2015 David Young
 *
 *
 */
namespace RDev\Tests\Authentication\Credentials\Storage\Mocks;
use RDev\Authentication\Credentials;
use RDev\Authentication\Credentials\Storage;
use RDev\HTTP\Responses;

class CredentialStorage implements Storage\ICredentialStorage
{
    /** @var Credentials\ICredential The list of credentials in storage */
    private $credential = null;
    /** @var string The unhashed token */
    private $unhashedToken = "";

    /**
     * {@inheritdoc}
     */
    public function delete(Responses\Response $response)
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
    public function save(Responses\Response $response, Credentials\ICredential $credential, $unhashedToken)
    {
        $this->credential = $credential;
        $this->unhashedToken = $unhashedToken;
    }
} 