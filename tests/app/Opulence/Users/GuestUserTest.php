<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Users;

/**
 * Tests the guest user class
 */
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