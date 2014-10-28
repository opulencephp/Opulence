<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the credential class
 */
namespace RDev\Authentication\Credentials;
use RDev\Tests\Cryptography\Mocks;

class CredentialTest extends \PHPUnit_Framework_TestCase
{
    /** @var Credential The credential to use in tests */
    private $credential = null;
    /** @var int|string The Id of the credential */
    private $id = -1;
    /** @var int The Id of the entity whose credential this is */
    private $entityId = -1;
    /** @var int The Id of the type of entity whose credential this is */
    private $entityTypeId = -1;
    /** @var Mocks\Token The token to use in tests */
    private $token = null;
    /** @var int The type of credential */
    private $type = -1;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->id = 321;
        $this->type = CredentialTypes::LOGIN;
        $this->entityId = 1;
        $this->entityTypeId = 844;
        $this->token = Mocks\Token::create();
        $this->credential = new Credential($this->id, $this->type, $this->entityId, $this->entityTypeId, $this->token);
    }

    /**
     * Tests checking if a credential is active
     */
    public function testCheckingIfActive()
    {
        $this->assertTrue($this->credential->isActive());
    }

    /**
     * Tests cloning the credential
     */
    public function testCloning()
    {
        $clone = clone $this->credential;
        $this->assertNotSame($clone, $this->credential);
        $this->assertNotSame($clone->getToken(), $this->credential->getToken());
    }

    /**
     * Tests deactivating a credential
     */
    public function testDeactivating()
    {
        $this->credential->deactivate();
        $this->assertFalse($this->credential->isActive());
        $this->assertFalse($this->credential->getToken()->isActive());
    }

    /**
     * Tests getting the entity Id
     */
    public function testGettingEntityId()
    {
        $this->assertEquals($this->entityId, $this->credential->getEntityId());
    }

    /**
     * Tests getting the entity type Id
     */
    public function testGettingEntityTypeId()
    {
        $this->assertEquals($this->entityTypeId, $this->credential->getEntityTypeId());
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals($this->id, $this->credential->getId());
    }

    /**
     * Tests getting the token
     */
    public function testGettingToken()
    {
        $this->assertSame($this->token, $this->credential->getToken());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType()
    {
        $this->assertEquals($this->type, $this->credential->getTypeId());
    }

    /**
     * Tests setting the entity Id
     */
    public function testSettingEntityId()
    {
        $this->credential->setEntityId(987);
        $this->assertEquals(987, $this->credential->getEntityId());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $this->credential->setId(724);
        $this->assertEquals(724, $this->credential->getId());
    }
} 