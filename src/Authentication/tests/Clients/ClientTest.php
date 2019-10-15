<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Authentication\Tests\Clients;

use Opulence\Authentication\Clients\Client;
use PHPUnit\Framework\TestCase;

/**
 * Tests the client
 */
class ClientTest extends TestCase
{
    private Client $client;

    protected function setUp(): void
    {
        $this->client = new Client(123, 'foo', 'bar');
    }

    public function testGettingId(): void
    {
        $this->assertEquals(123, $this->client->getId());
    }

    public function testGettingName(): void
    {
        $this->assertEquals('foo', $this->client->getName());
    }

    public function testGettingSecret(): void
    {
        $this->assertEquals('bar', $this->client->getSecret());
    }

    public function testSettingId(): void
    {
        $this->client->setId('new');
        $this->assertEquals('new', $this->client->getId());
    }

    public function testSettingName(): void
    {
        $this->client->setName('new');
        $this->assertEquals('new', $this->client->getName());
    }
}
