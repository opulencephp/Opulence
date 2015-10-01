<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the path matcher
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

class PathMatcherTest extends \PHPUnit_Framework_TestCase
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
        $this->request = $this->getMock(Request::class, [], [], "", false);
        $this->route = $this->getMock(ParsedRoute::class, [], [], "", false);
    }

    /**
     * Tests that there is a match when the regex matches
     */
    public function testMatchWithMatchingRegex()
    {
        $this->route->expects($this->any())->method("getPathRegex")->willReturn("#^foo$#");
        $this->request->expects($this->any())->method("getPath")->willReturn("foo");
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is not match when the regex does not match
     */
    public function testNoMatchWithNoMatchingRegex()
    {
        $this->route->expects($this->any())->method("getPathRegex")->willReturn("#^foo$#");
        $this->request->expects($this->any())->method("getPath")->willReturn("bar");
        $this->assertFalse($this->matcher->isMatch($this->route, $this->request));
    }
}