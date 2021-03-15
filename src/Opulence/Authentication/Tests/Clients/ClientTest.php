<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Authentication\Tests\Clients;

use Opulence\Authentication\Clients\Client;

/**
 * Tests the client
 */
class ClientTest extends \PHPUnit\Framework\TestCase
{
    /** @var Client The client to use in tests */
    private $client = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->client = new Client(123, 'foo', 'bar');
    }

    /**
     * Tests getting the Id
     */
    public function testGettingId()
    {
        $this->assertEquals(123, $this->client->getId());
    }

    /**
     * Tests getting the name
     */
    public function testGettingName()
    {
        $this->assertEquals('foo', $this->client->getName());
    }

    /**
     * Tests getting the secret
     */
    public function testGettingSecret()
    {
        $this->assertEquals('bar', $this->client->getSecret());
    }

    /**
     * Tests setting the Id
     */
    public function testSettingId()
    {
        $this->client->setId('new');
        $this->assertEquals('new', $this->client->getId());
    }

    /**
     * Tests setting the name
     */
    public function testSettingName()
    {
        $this->client->setName('new');
        $this->assertEquals('new', $this->client->getName());
    }
}
