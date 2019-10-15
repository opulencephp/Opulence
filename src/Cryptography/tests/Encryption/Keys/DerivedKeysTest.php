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
use PHPUnit\Framework\TestCase;

/**
 * Tests the derived keys
 */
class DerivedKeysTest extends TestCase
{
    private DerivedKeys $derivedKeys;

    protected function setUp(): void
    {
        $this->derivedKeys = new DerivedKeys(str_repeat('1', 32), str_repeat('2', 32));
    }

    public function testGettingAuthenticationKey(): void
    {
        $this->assertEquals(str_repeat('2', 32), $this->derivedKeys->getAuthenticationKey());
    }

    public function testGettingEncryptionKey(): void
    {
        $this->assertEquals(str_repeat('1', 32), $this->derivedKeys->getEncryptionKey());
    }
}
