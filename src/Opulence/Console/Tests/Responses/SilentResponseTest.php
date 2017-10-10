<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses;

use Opulence\Console\Responses\SilentResponse;

/**
 * Tests the silent response
 */
class SilentResponseTest extends \PHPUnit\Framework\TestCase
{
    /** @var SilentResponse The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new SilentResponse();
    }

    /**
     * Tests writing without a new line
     */
    public function testWrite()
    {
        ob_start();
        $this->response->write('foo');
        $this->assertEmpty(ob_get_clean());
    }

    /**
     * Tests writing with a new line
     */
    public function testWriteln()
    {
        ob_start();
        $this->response->writeln('foo');
        $this->assertEmpty(ob_get_clean());
    }
}
