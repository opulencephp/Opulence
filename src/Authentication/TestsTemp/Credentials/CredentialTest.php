<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\TestsTemp\Credentials;

use Opulence\Authentication\Credentials\Credential;

/**
 * Tests the credentials
 */
class CredentialTest extends \PHPUnit\Framework\TestCase
{
    /** @var Credential The credential to use in tests */
    private Credential $credential;

    protected function setUp(): void
    {
        $this->credential = new Credential('foo', ['bar' => 'baz']);
    }

    public function testGettingType(): void
    {
        $this->assertEquals('foo', $this->credential->getType());
    }

    public function testGettingValue(): void
    {
        $this->assertEquals('baz', $this->credential->getValue('bar'));
    }

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
