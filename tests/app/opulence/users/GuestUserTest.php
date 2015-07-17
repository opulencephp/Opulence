<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the guest user class
 */
namespace Opulence\Users;

class GuestUserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $user = new GuestUser();
        $this->assertEquals(-1, $user->getId());
    }

    /**
     * Tests getting the roles
     */
    public function testGettingRoles()
    {
        $user = new GuestUser();
        $this->assertEquals([], $user->getRoles());
    }
} 