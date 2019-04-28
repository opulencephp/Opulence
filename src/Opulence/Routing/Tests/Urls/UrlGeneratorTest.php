<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Tests\Urls;

use Opulence\Http\Requests\RequestMethods;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Routes\Route;
use Opulence\Routing\Routes\RouteCollection;
use Opulence\Routing\Urls\UrlException;
use Opulence\Routing\Urls\UrlGenerator;

/**
 * Tests the URL generator
 */
class UrlGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /** @var UrlGenerator The generator to use in tests */
    private $generator;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $namedRoutes = [
            new Route(
                RequestMethods::GET,
                '/users',
                'foo@bar',
                ['name' => 'pathNoParameters']
            ),
            new Route(
                RequestMethods::GET,
                '/users/:userId',
                'foo@bar',
                ['name' => 'pathOneParameter']
            ),
            new Route(
                RequestMethods::GET,
                '/users/:userId/profile/:mode',
                'foo@bar',
                ['name' => 'pathTwoParameters']
            ),
            new Route(
                RequestMethods::GET,
                '/users[:foo]',
                'foo@bar',
                ['name' => 'pathOptionalVariable']
            ),
            new Route(
                RequestMethods::GET,
                '/users[/:foo]',
                'foo@bar',
                ['name' => 'pathOptionalSlashAndVariable']
            ),
            new Route(
                RequestMethods::GET,
                '/users[/:foo[/:bar]]',
                'foo@bar',
                ['name' => 'pathOptionalNestedSlashesAndVariables']
            ),
            new Route(
                RequestMethods::GET,
                '/users/:userId',
                'foo@bar',
                [
                    'vars' => ['userId' => "\d+"],
                    'name' => 'pathVariableRegex'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users',
                'foo@bar',
                [
                    'host' => 'example.com',
                    'name' => 'hostNoParameters'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users',
                'foo@bar',
                [
                    'host' => ':subdomain.example.com',
                    'name' => 'hostOneParameter'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users',
                'foo@bar',
                [
                    'host' => ':subdomain1.:subdomain2.example.com',
                    'name' => 'hostTwoParameters'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users',
                'foo@bar',
                [
                    'host' => '[:subdomain]example.com',
                    'name' => 'hostOptionalVariable'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users/:userId/profile/:mode',
                'foo@bar',
                [
                    'host' => ':subdomain1.:subdomain2.example.com',
                    'name' => 'hostAndPathMultipleParameters'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users[:foo]',
                'foo@bar',
                [
                    'host' => '[:subdomain]example.com',
                    'name' => 'hostAndPathOptionalParameters'
                ]
            ),
            new Route(
                RequestMethods::GET,
                '/users',
                'foo@bar',
                [
                    'host' => 'foo.example.com',
                    'https' => true,
                    'name' => 'secureHostNoParameters'
                ]
            )
        ];
        $routeCollection = new RouteCollection();
        $parser = new Parser();

        foreach ($namedRoutes as $name => $route) {
            $routeCollection->add($parser->parse($route));
        }

        $this->generator = new UrlGenerator($routeCollection);
    }

    /**
     * Tests generating an HTTPS URL
     */
    public function testGeneratingHttpsUrl(): void
    {
        $this->assertEquals(
            'https://foo.example.com/users',
            $this->generator->createFromName('secureHostNoParameters')
        );
        $this->assertEquals(
            '#^' . preg_quote('https://foo.example.com/users', '#') . '$#',
            $this->generator->createRegexFromName('secureHostNoParameters')
        );
    }

    /**
     * Tests generating a route for a non-existent route
     */
    public function testGeneratingUrlForNonExistentRoute(): void
    {
        $this->assertEmpty($this->generator->createFromName('foo'));
        $this->assertEquals('#^.*$#', $this->generator->createRegexFromName('foo'));
    }

    /**
     * Tests generating a URL with multiple host and path values
     */
    public function testGeneratingUrlWithMultipleHostAndPathValues(): void
    {
        $this->assertEquals(
            'http://foo.bar.example.com/users/23/profile/edit',
            $this->generator->createFromName('hostAndPathMultipleParameters', 'foo', 'bar', 23, 'edit')
        );
        $this->assertEquals(
            "#^http\://(?P<subdomain1>[^\/:]+)\.(?P<subdomain2>[^\/:]+)\.example\.com/users/(?P<userId>[^\/:]+)/profile/(?P<mode>[^\/:]+)$#",
            $this->generator->createRegexFromName('hostAndPathMultipleParameters')
        );
    }

    /**
     * Tests generating a URL with no values
     */
    public function testGeneratingUrlWithNoValues(): void
    {
        $this->assertEquals('/users', $this->generator->createFromName('pathNoParameters'));
        $this->assertEquals('http://example.com/users', $this->generator->createFromName('hostNoParameters'));
        $this->assertEquals('#^/users$#', $this->generator->createRegexFromName('pathNoParameters'));
        $this->assertEquals(
            "#^http\://example\.com/users$#",
            $this->generator->createRegexFromName('hostNoParameters')
        );
    }

    /**
     * Tests generating a URL with one value
     */
    public function testGeneratingUrlWithOneValue(): void
    {
        $this->assertEquals('/users/23', $this->generator->createFromName('pathOneParameter', 23));
        $this->assertEquals(
            'http://foo.example.com/users',
            $this->generator->createFromName('hostOneParameter', 'foo')
        );
        $this->assertEquals(
            "#^/users/(?P<userId>[^\/:]+)$#",
            $this->generator->createRegexFromName('pathOneParameter')
        );
        $this->assertEquals(
            "#^http\://(?P<subdomain>[^\/:]+)\.example\.com/users$#",
            $this->generator->createRegexFromName('hostOneParameter')
        );
    }

    /**
     * Tests generating a URL with an optional host variable
     */
    public function testGeneratingUrlWithOptionalHostVariable(): void
    {
        $this->assertEquals(
            'http://example.com/users',
            $this->generator->createFromName('hostOptionalVariable')
        );
        $this->assertEquals(
            "#^http\://(?:(?P<subdomain>[^\/:]+))?example\.com/users$#",
            $this->generator->createRegexFromName('hostOptionalVariable')
        );
    }

    /**
     * Tests generating a URL with optional nested slashes and path variables
     */
    public function testGeneratingUrlWithOptionalNestedSlashesAndPathVariables(): void
    {
        $this->assertEquals(
            '/users',
            $this->generator->createFromName('pathOptionalNestedSlashesAndVariables')
        );
        $this->assertEquals(
            '/users/bar',
            $this->generator->createFromName('pathOptionalNestedSlashesAndVariables', 'bar')
        );
        $this->assertEquals(
            '/users/bar/baz',
            $this->generator->createFromName('pathOptionalNestedSlashesAndVariables', 'bar', 'baz')
        );
    }

    /**
     * Tests generating a URL with an optional path variable
     */
    public function testGeneratingUrlWithOptionalPathVariable(): void
    {
        $this->assertEquals(
            '/users',
            $this->generator->createFromName('pathOptionalVariable')
        );
        $this->assertEquals(
            "#^/users(?:(?P<foo>[^\/:]+))?$#",
            $this->generator->createRegexFromName('pathOptionalVariable')
        );
    }

    /**
     * Tests generating a URL with an optional slash and path variable
     */
    public function testGeneratingUrlWithOptionalSlashAndPathVariable(): void
    {
        $this->assertEquals(
            '/users',
            $this->generator->createFromName('pathOptionalSlashAndVariable')
        );
        $this->assertEquals(
            '/users/bar',
            $this->generator->createFromName('pathOptionalSlashAndVariable', 'bar')
        );
    }

    /**
     * Tests generating a URL with optional variables in the path and host
     */
    public function testGeneratingUrlWithOptionalVariablesInPathAndHost(): void
    {
        $this->assertEquals(
            'http://example.com/users',
            $this->generator->createFromName('hostAndPathOptionalParameters')
        );
    }

    /**
     * Tests generating a URL with two values
     */
    public function testGeneratingUrlWithTwoValues(): void
    {
        $this->assertEquals(
            '/users/23/profile/edit',
            $this->generator->createFromName('pathTwoParameters', 23, 'edit')
        );
        $this->assertEquals(
            'http://foo.bar.example.com/users',
            $this->generator->createFromName('hostTwoParameters', 'foo', 'bar')
        );
    }

    /**
     * Tests generating a URL with a variable value that does not satisfy the regex
     */
    public function testGeneratingUrlWithVariableThatDoesNotSatisfyRegex(): void
    {
        $this->expectException(UrlException::class);
        $this->generator->createFromName('pathVariableRegex', 'notANumber');
    }

    /**
     * Tests not filling all values in a host
     */
    public function testNotFillingAllHostValues(): void
    {
        $this->expectException(UrlException::class);
        $this->generator->createFromName('hostOneParameter');
    }

    /**
     * Tests not filling all values in a path
     */
    public function testNotFillingAllPathValues(): void
    {
        $this->expectException(UrlException::class);
        $this->generator->createFromName('pathOneParameter');
    }

    /**
     * Tests passing in a non array value
     */
    public function testPassingNonArrayValue(): void
    {
        $this->assertEquals('/users/23', $this->generator->createFromName('pathOneParameter', 23));
    }
}
