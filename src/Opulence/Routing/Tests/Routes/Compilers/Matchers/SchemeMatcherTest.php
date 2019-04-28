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
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\ParsedRoute;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Tests the scheme matcher
 */
class SchemeMatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var SchemeMatcher The matcher to use in tests */
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
        $this->matcher = new SchemeMatcher();
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->route = $this->getMockBuilder(ParsedRoute::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Tests that there is a match when on an HTTP scheme with an insecure route
     */
    public function testMatchOnHttpWithInsecureRoute(): void
    {
        $this->route->expects($this->any())->method('isSecure')->willReturn(false);
        $this->request->expects($this->any())->method('isSecure')->willReturn(false);
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is a match when on an HTTPS scheme with a secure route
     */
    public function testMatchOnHttpsWithSecureRoute(): void
    {
        $this->route->expects($this->any())->method('isSecure')->willReturn(true);
        $this->request->expects($this->any())->method('isSecure')->willReturn(true);
        $this->assertTrue($this->matcher->isMatch($this->route, $this->request));
    }

    /**
     * Tests that there is no match when on an HTTP scheme with a secure route
     */
    public function testNoMatchOnHttpWithSecureRoute(): void
    {
        $this->route->expects($this->any())->method('isSecure')->willReturn(true);
        $this->request->expects($this->any())->method('isSecure')->willReturn(false);
        $this->assertFalse($this->matcher->isMatch($this->route, $this->request));
    }
}
