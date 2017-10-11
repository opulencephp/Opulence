<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes\Compilers\Matchers;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Tests the path matcher
 */
class PathMatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var PathMatcher The matcher to use in tests */
    private $matcher = null;
    /** @var Request|\PHPUnit_Framework_MockObject_MockObject The request to use in tests */
    private $request = null;
    /** @var ParsedRoute|\PHPUnit_Framework_MockObject_MockObject The route to use in tests */
    private $route = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->matcher = new PathMatcher();
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
    public function testMatchWithMatchingRegex()
    {
        $this->route->expects($this->any())->method('getPathRegex')->willReturn('#^foo$#');
        $this->request->expects($this->any())->method('getPath')->willReturn('foo');
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is not match when the regex does not match
     */
    public function testNoMatchWithNoMatchingRegex()
    {
        $this->route->expects($this->any())->method('getPathRegex')->willReturn('#^foo$#');
        $this->request->expects($this->any())->method('getPath')->willReturn('bar');
        $this->assertFalse($this->matcher->isMatch($this->route, $this->request));
    }
}
