<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication;

/**
 * Tests the principal
 */
class PrincipalTest extends \PHPUnit_Framework_TestCase
{
    /** @var Principal The principal to use in tests */
    private $principal = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->principal = new Principal("foo", "bar");
    }

    /**
     * Tests getting the identity
     */
    public function testGettingIdentity()
    {
        $this->assertEquals("bar", $this->principal->getIdentity());
    }

    /**
     * Tests getting the type
     */
    public function testGettingType()
    {
        $this->assertEquals("foo", $this->principal->getType());
    }
}