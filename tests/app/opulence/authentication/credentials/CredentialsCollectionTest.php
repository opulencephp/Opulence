<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the credentials class
 */
namespace Opulence\Authentication\Credentials;

use Opulence\HTTP\Responses\Response;
use Opulence\Tests\Authentication\Credentials\Storage\Mocks\CredentialStorage;
use Opulence\Tests\Authentication\Tokens\Mocks\Token;
use RuntimeException;

class CredentialsCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding a credential
     */
    public function testAddingCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $collection->add($credential);
        $this->assertEquals($credential, $collection->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests adding credentials without registering their storage
     */
    public function testAddingCredentialWithoutStorage()
    {
        $this->setExpectedException(RuntimeException::class);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection = new CredentialCollection(1, 369);
        $collection->add($credential);
    }

    /**
     * Tests adding an expired credential
     */
    public function testAddingDeactivatedCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credential->deactivate();
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $collection->add($credential);
        $this->assertFalse($collection->has(CredentialTypes::LOGIN));
        $this->assertNull($collection->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests checking for a credential that exists in storage but has not been added
     */
    public function testCheckingForCredentialThatIsInStorageButHasNotBeenAdded()
    {
        $collection = new CredentialCollection(1, 369);
        $storage = new CredentialStorage();
        $collection->registerStorage(CredentialTypes::LOGIN, $storage);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $storage->save(new Response(), $credential, "foo");
        $this->assertTrue($collection->has(CredentialTypes::LOGIN));
        $this->assertSame($credential, $collection->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests checking if it has a credential
     */
    public function testCheckingIfHasCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $this->assertFalse($collection->has(CredentialTypes::LOGIN));
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $collection->add($credential);
        $this->assertTrue($collection->has(CredentialTypes::LOGIN));
    }

    /**
     * Tests deleting a credential
     */
    public function testDeletingCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $collection->add($credential);
        $this->assertTrue($credential->isActive());
        $collection->delete(new Response(), CredentialTypes::LOGIN);
        $this->assertFalse($collection->has(CredentialTypes::LOGIN));
        $this->assertFalse($credential->isActive());
    }

    /**
     * Tests getting all the types
     */
    public function testGettingAllTypes()
    {
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $collection->add($credential);
        $this->assertEquals([CredentialTypes::LOGIN], $collection->getTypes());
    }

    /**
     * Tests getting a credential
     */
    public function testGettingCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $collection->add($credential);
        $this->assertSame($credential, $collection->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests getting a credential that exists in storage but has not been added
     */
    public function testGettingCredentialThatExistsInStorageButHasNotBeenAdded()
    {
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $storage = new CredentialStorage();
        $collection->registerStorage(CredentialTypes::LOGIN, $storage);
        $storage->save(new Response(), $credential, "foo");
        $this->assertEquals($credential, $collection->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests getting a deactivated credential
     */
    public function testGettingDeactivatedCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $credential->deactivate();
        $collection->add($credential);
        $this->assertNull($collection->get(CredentialTypes::LOGIN));
        $this->assertEquals([], $collection->getTypes());
        $this->assertEquals([], $collection->getAll());
    }

    /**
     * Tests getting the entity Id
     */
    public function testGettingEntityId()
    {
        $collection = new CredentialCollection(1, 369);
        $this->assertEquals(1, $collection->getEntityId());
    }

    /**
     * Tests getting the entity type Id
     */
    public function testGettingEntityTypeId()
    {
        $collection = new CredentialCollection(1, 369);
        $this->assertEquals(369, $collection->getEntityTypeId());
    }

    /**
     * Tests instantiating the credentials with a list of credentials
     */
    public function testInstantiatingWithListOfCredentials()
    {
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $storage = new CredentialStorage();
        $collection = new CredentialCollection(1, 369, [CredentialTypes::LOGIN => $storage], [$credential]);
        $this->assertEquals([$credential], $collection->getAll());
    }

    /**
     * Tests instantiating the credentials with a list of credentials without storage
     */
    public function testInstantiatingWithListOfCredentialsWithoutStorage()
    {
        $this->setExpectedException(RuntimeException::class);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        new CredentialCollection(1, 369, [], [$credential]);
    }

    /**
     * Tests instantiating the credentials without a list of credentials
     */
    public function testInstantiatingWithoutListOfCredentials()
    {
        $collection = new CredentialCollection(1, 369);
        $this->assertEquals([], $collection->getAll());
    }

    /**
     * Tests not passing in the entity Id or entity type Id
     */
    public function testNotPassingInEntityIdOrEntityTypeId()
    {
        $collection = new CredentialCollection();
        $this->assertEquals(-1, $collection->getEntityId());
        $this->assertEquals(-1, $collection->getEntityTypeId());
    }

    /**
     * Tests removing a credential that does not have a storage registered
     */
    public function testRemovingCredentialThatDoesNotHaveStorage()
    {
        $this->setExpectedException(RuntimeException::class);
        $collection = new CredentialCollection(1, 369);
        $collection->delete(new Response(), CredentialTypes::LOGIN);
    }

    /**
     * Tests saving a credential
     */
    public function testSavingCredential()
    {
        $collection = new CredentialCollection(1, 369);
        $collection->registerStorage(CredentialTypes::LOGIN, new CredentialStorage());
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->save(new Response(), $credential, "foo");
        $this->assertSame($credential, $collection->get(CredentialTypes::LOGIN));
    }

    /**
     * Tests saving a credential without registering a storage mechanism
     */
    public function testSavingCredentialWithNoStorageRegistered()
    {
        $this->setExpectedException(RuntimeException::class);
        $collection = new CredentialCollection(1, 369);
        $credential = new Credential(321, CredentialTypes::LOGIN, 1, 844, Token::create());
        $collection->save(new Response(), $credential, "foo");
    }

    /**
     * Tests setting the entity Id
     */
    public function testSettingEntityId()
    {
        $collection = new CredentialCollection(-1, -1);
        $collection->setEntityId(44);
        $this->assertEquals(44, $collection->getEntityId());
    }

    /**
     * Tests setting the entity type Id
     */
    public function testSettingTypeEntityId()
    {
        $collection = new CredentialCollection(-1, -1);
        $collection->setEntityTypeId(44);
        $this->assertEquals(44, $collection->getEntityTypeId());
    }
} 