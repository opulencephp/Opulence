<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the credentials class
 */
namespace RDev\Models\Authentication\Credentials;
use RDev\Tests\Models\Authentication\Credentials\Storage\Mocks\CredentialStorage;
use RDev\Tests\Models\Cryptography\Mocks\Token;

class CredentialsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a credential
     */
    public function testAddingCredential()
    {
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credentials->add($credential);
        $this->assertEquals($credential, $credentials->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests adding credentials without registering their storage
     */
    public function testAddingCredentialWithoutStorage()
    {
        $this->setExpectedException("\\RuntimeException");
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials = new Credentials(1, 369);
        $credentials->add($credential);
    }

    /**
     * Tests adding an expired credential
     */
    public function testAddingDeactivatedCredential()
    {
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credential->deactivate();
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credentials->add($credential);
        $this->assertFalse($credentials->has(CredentialTypes::LOGIN));
        $this->assertNull($credentials->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests checking for a credential that exists in storage but has not been added
     */
    public function testCheckingForCredentialThatIsInStorageButHasNotBeenAdded()
    {
        $credentials = new Credentials(1, 369);
        $storage = new CredentialStorage();
        $credentials->registerStorage(CredentialTypes::LOGIN, $storage);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $storage->save($credential, "foo");
        $this->assertTrue($credentials->has(CredentialTypes::LOGIN));
        $this->assertSame($credential, $credentials->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests checking if it has a credential
     */
    public function testCheckingIfHasCredential()
    {
        $credentials = new Credentials(1, 369);
        $this->assertFalse($credentials->has(CredentialTypes::LOGIN));
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credentials->add($credential);
        $this->assertTrue($credentials->has(CredentialTypes::LOGIN));
    }

    /**
     * Tests deleting a credential
     */
    public function testDeletingCredential()
    {
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credentials->add($credential);
        $this->assertTrue($credential->isActive());
        $credentials->delete(CredentialTypes::LOGIN);
        $this->assertFalse($credentials->has(CredentialTypes::LOGIN));
        $this->assertFalse($credential->isActive());
    }

    /**
     * Tests getting all the types
     */
    public function testGettingAllTypes()
    {
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credentials->add($credential);
        $this->assertEquals([CredentialTypes::LOGIN], $credentials->getTypes());
    }

    /**
     * Tests getting a credential
     */
    public function testGettingCredential()
    {
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credentials->add($credential);
        $this->assertSame($credential, $credentials->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests getting a credential that exists in storage but has not been added
     */
    public function testGettingCredentialThatExistsInStorageButHasNotBeenAdded()
    {
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $storage = new CredentialStorage();
        $credentials->registerStorage(CredentialTypes::LOGIN, $storage);
        $storage->save($credential, "foo");
        $this->assertEquals($credential, $credentials->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests getting a deactivated credential
     */
    public function testGettingDeactivatedCredential()
    {
        $credentials = new Credentials(1, 369);
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credential->deactivate();
        $credentials->add($credential);
        $this->assertNull($credentials->get(CredentialTypes::LOGIN));
        $this->assertEquals([], $credentials->getTypes());
        $this->assertEquals([], $credentials->getAll());
    }

    /**
     * Tests getting the entity Id
     */
    public function testGettingEntityId()
    {
        $credentials = new Credentials(1, 369);
        $this->assertEquals(1, $credentials->getEntityId());
    }

    /**
     * Tests getting the entity type Id
     */
    public function testGettingEntityTypeId()
    {
        $credentials = new Credentials(1, 369);
        $this->assertEquals(369, $credentials->getEntityTypeId());
    }

    /**
     * Tests instantiating the credentials with a list of credentials
     */
    public function testInstantiatingWithListOfCredentials()
    {
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $storage = new CredentialStorage();
        $credentials = new Credentials(1, 369, [CredentialTypes::LOGIN => $storage], [$credential]);
        $this->assertEquals([$credential], $credentials->getAll());
    }

    /**
     * Tests instantiating the credentials with a list of credentials without storage
     */
    public function testInstantiatingWithListOfCredentialsWithoutStorage()
    {
        $this->setExpectedException("\\RuntimeException");
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        new Credentials(1, 369, [], [$credential]);
    }

    /**
     * Tests instantiating the credentials without a list of credentials
     */
    public function testInstantiatingWithoutListOfCredentials()
    {
        $credentials = new Credentials(1, 369);
        $this->assertEquals([], $credentials->getAll());
    }

    /**
     * Tests removing a credential that does not have a storage registered
     */
    public function testRemovingCredentialThatDoesNotHaveStorage()
    {
        $this->setExpectedException("\\RuntimeException");
        $credentials = new Credentials(1, 369);
        $credentials->delete(CredentialTypes::LOGIN);
    }

    /**
     * Tests saving a credential
     */
    public function testSavingCredential()
    {
        $credentials = new Credentials(1, 369);
        $credentials->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->save($credential, "foo");
        $this->assertSame($credential, $credentials->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests saving a credential without registering a storage mechanism
     */
    public function testSavingCredentialWithNoStorageRegistered()
    {
        $this->setExpectedException("\\RuntimeException");
        $credentials = new Credentials(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credentials->save($credential, "foo");
    }
} 