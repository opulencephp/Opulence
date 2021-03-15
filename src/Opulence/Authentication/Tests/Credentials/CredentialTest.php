<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Credentials;

use Opulence\Authentication\Credentials\Credential;

/**
 * Tests the credentials
 */
class CredentialTest extends \PHPUnit\Framework\TestCase
{
    /** @var Credential The credential to use in tests */
    private $credential = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->credential = new Credential('foo', ['bar' => 'baz']);
    }

    /**
     * Tests getting the type
     */
    public function testGettingType()
    {
        $this->assertEquals('foo', $this->credential->getType());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue()
    {
        $this->assertEquals('baz', $this->credential->getValue('bar'));
    }

    /**
     * Tests getting the values
     */
    public function testGettingValues()
    {
        $this->assertEquals(['bar' => 'baz'], $this->credential->getValues());
    }

    /**
     * Tests that non-existent values return null
     */
    public function testNullReturnedForNonExistentValues()
    {
        $this->assertNull($this->credential->getValue('doesNotExist'));
    }
}
