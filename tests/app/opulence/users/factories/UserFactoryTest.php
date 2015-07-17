<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the user factory
 */
namespace Opulence\Users\Factories;
use DateTime;
use Opulence\Users\User;

class UserFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var UserFactory The user factory to test */
    private $userFactory = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->userFactory = new UserFactory();
    }

    /**
     * Tests creating a user
     */
    public function testCreatingUser()
    {
        $user = $this->userFactory->createUser(1, new DateTime("1776-07-04 12:34:56"), [1, 2, 3]);
        $this->assertInstanceOf(User::class, $user);
    }
} 