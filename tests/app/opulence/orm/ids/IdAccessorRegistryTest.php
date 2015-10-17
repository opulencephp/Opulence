<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Id accessor registry
 */
namespace Opulence\ORM\Ids;

use Opulence\ORM\ORMException;
use Opulence\Tests\Mocks\User;

class IdAccessorRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var IdAccessorRegistry The registry to use in tests */
    private $registry = null;
    /** @var User An entity to use in the tests */
    private $entity1 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new IdAccessorRegistry();
        $this->registry->registerIdAccessors(
            User::class,
            function ($user) {
                /** @var User $user */
                return $user->getId();
            },
            function ($user, $id) {
                /** @var User $user */
                $user->setId($id);
            }
        );
        $this->entity1 = new User(724, "foo");
    }

    /**
     * Tests getting an entity Id
     */
    public function testGettingEntityId()
    {
        $this->assertEquals(724, $this->registry->getEntityId($this->entity1));
    }

    /**
     * Tests getting an entity Id without registering a getter
     */
    public function testGettingEntityIdWithoutRegisteringGetter()
    {
        $this->setExpectedException(ORMException::class);
        $entity = $this->getMock(User::class, [], [], "Foo", false);
        $this->registry->getEntityId($entity);
    }

    /**
     * Tests setting an entity Id
     */
    public function testSettingEntityId()
    {
        $this->registry->setEntityId($this->entity1, 333);
        $this->assertEquals(333, $this->entity1->getId());
    }

    /**
     * Tests setting an entity Id without registering a setter
     */
    public function testSettingEntityIdWithoutRegisteringGetter()
    {
        $this->setExpectedException(ORMException::class);
        $entity = $this->getMock(User::class, [], [], "Foo", false);
        $this->registry->setEntityId($entity, 24);
    }
}