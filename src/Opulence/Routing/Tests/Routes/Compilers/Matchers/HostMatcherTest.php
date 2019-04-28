<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Routes\Compilers\Matchers;

use Opulence\Http\Requests\Request;
use Opulence\Http\Requests\RequestHeaders;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\ParsedRoute;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the host matcher
 */
class HostMatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var HostMatcher The matcher to use in tests */
    private $matcher;
    /** @var Request|MockObject The request to use in tests */
    private $request;
    /** @var ParsedRoute|MockObject The route to use in tests */
    private $route;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->matcher = new HostMatcher();
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->route = $this->getMockBuilder(ParsedRoute::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests that there is a match when the regex matches
     */
    public function testMatchWithMatchingRegex(): void
    {
        $this->route->expects($this->any())->method('getHostRegex')->willReturn('#^foo$#');
        $headers = $this->createMock(RequestHeaders::class);
        $headers->expects($this->any())->method('get')->with('HOST')->willReturn('foo');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($headers);
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is not match when the regex does not match
     */
    public function testNoMatchWithNoMatchingRegex(): void
    {
        $this->route->expects($this->any())->method('getHostRegex')->willReturn('#^foo$#');
        $headers = $this->createMock(RequestHeaders::class);
        $headers->expects($this->any())->method('get')->with('HOST')->willReturn('#^bar$#');
        $this->request->expects($this->any())->method('getHeaders')->willReturn($headers);
        $this->assertFalse($this->matcher->isMatch($this->route, $this->request));
    }
}
