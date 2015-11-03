<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Users\Factories;

use DateTime;
use Opulence\Users\User;

/**
 * Tests the user factory
 */
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