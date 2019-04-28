<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cryptography\Tests\Encryption\Keys;

use Opulence\Cryptography\Encryption\Keys\DerivedKeys;

/**
 * Tests the derived keys
 */
class DerivedKeysTest extends \PHPUnit\Framework\TestCase
{
    /** @var DerivedKeys The derived keys to use in tests */
    private $derivedKeys;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->derivedKeys = new DerivedKeys(str_repeat('1', 32), str_repeat('2', 32));
    }

    /**
     * Tests getting the authentication key
     */
    public function testGettingAuthenticationKey(): void
    {
        $this->assertEquals(str_repeat('2', 32), $this->derivedKeys->getAuthenticationKey());
    }

    /**
     * Tests getting the encryption key
     */
    public function testGettingEncryptionKey(): void
    {
        $this->assertEquals(str_repeat('1', 32), $this->derivedKeys->getEncryptionKey());
    }
}
