<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Credentials;

use Opulence\Authentication\Credentials\Credential;

/**
 * Tests the credentials
 */
class CredentialTest extends \PHPUnit\Framework\TestCase
{
    /** @var Credential The credential to use in tests */
    private Credential $credential;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->credential = new Credential('foo', ['bar' => 'baz']);
    }

    /**
     * Tests getting the type
     */
    public function testGettingType(): void
    {
        $this->assertEquals('foo', $this->credential->getType());
    }

    /**
     * Tests getting the value
     */
    public function testGettingValue(): void
    {
        $this->assertEquals('baz', $this->credential->getValue('bar'));
    }

    /**
     * Tests getting the values
     */
    public function testGettingValues(): void
    {
        $this->assertEquals(['bar' => 'baz'], $this->credential->getValues());
    }

    /**
     * Tests that non-existent values return null
     */
    public function testNullReturnedForNonExistentValues(): void
    {
        $this->assertNull($this->credential->getValue('doesNotExist'));
    }
}
