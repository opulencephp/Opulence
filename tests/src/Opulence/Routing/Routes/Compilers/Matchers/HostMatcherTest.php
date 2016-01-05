<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\Http\Headers;
use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Tests the host matcher
 */
class HostMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var HostMatcher The matcher to use in tests */
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
        $this->matcher = new HostMatcher();
        $this->request = $this->getMock(Request::class, [], [], "", false);
        $this->route = $this->getMock(ParsedRoute::class, [], [], "", false);
    }

    /**
     * Tests that there is a match when the regex matches
     */
    public function testMatchWithMatchingRegex()
    {
        $this->route->expects($this->any())->method("getHostRegex")->willReturn("#^foo$#");
        $headers = $this->getMock(Headers::class);
        $headers->expects($this->any())->method("get")->with("HOST")->willReturn("foo");
        $this->request->expects($this->any())->method("getHeaders")->willReturn($headers);
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is not match when the regex does not match
     */
    public function testNoMatchWithNoMatchingRegex()
    {
        $this->route->expects($this->any())->method("getHostRegex")->willReturn("#^foo$#");
        $headers = $this->getMock(Headers::class);
        $headers->expects($this->any())->method("get")->with("HOST")->willReturn("#^bar$#");
        $this->request->expects($this->any())->method("getHeaders")->willReturn($headers);
        $this->assertFalse($this->matcher->isMatch($this->route, $this->request));
    }
}