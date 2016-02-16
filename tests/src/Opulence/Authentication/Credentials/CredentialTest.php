<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Authentication\Credentials;

/**
 * Tests the credentials
 */
class CredentialTest extends \PHPUnit_Framework_TestCase
{
    /** @var Credential The credential to use in tests */
    private $credential = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->credential = new Credential(1, ["foo" => "bar"]);
    }

    /**
     * Tests getting the type Id
     */
    public function testGettingTypeId()
    {
        $this->assertEquals(1, $this->credential->getTypeId());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $this->assertEquals("bar", $this->credential->getValue("foo"));
    }

    /**
     * Tests getting the values
     */
    public function testGettingValues()
    {
        $this->assertEquals(["foo" => "bar"], $this->credential->getValues());
    }

    /**
     * Tests that non-existent values return null
     */
    public function testNullReturnedForNonExistentValues()
    {
        $this->assertNull($this->credential->getValue("doesNotExist"));
    }
}