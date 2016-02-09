<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Users;

/**
 * Tests the user object
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /** @var User The user object we're going to clone for our tests */
    private $user = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $this->user = new User(18175, "foo");
    }

    /**
     * Tests getting the hashed password
     */
    public function testGettingHashedPassword()
    {
        $this->assertEquals("foo", $this->user->getHashedPassword());
    }

    /**
     * Test getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals(18175, $this->user->getId());
    }

    /**
     * Tests setting the hashed password
     */
    public function testSettingHashedPassword()
    {
        $this->user->setHashedPassword("bar");
        $this->assertEquals("bar", $this->user->getHashedPassword());
    }

    /**
     * Test setting the Id
     */
    public function testSettingId()
    {
        $this->user->setId(12345);
        $this->assertEquals(12345, $this->user->getId());
    }
} 