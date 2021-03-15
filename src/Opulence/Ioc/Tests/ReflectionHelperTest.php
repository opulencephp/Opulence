<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests;

use Opulence\Ioc\ReflectionHelper;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use ReflectionFunction;

class ReflectionHelperTest extends TestCase
{
    /** @var ReflectionHelper */
    protected $sut;

    public function setUp() : void
    {
        $this->sut = new ReflectionHelper();
    }

    /**
     * @return array[]
     */
    public function getParameterClassNameProvider() : array
    {
        return [
            [
                function () {
                },
                [],
            ],
            [
                function ($a) {
                },
                [null],
            ],
            [
                function (bool $a) {
                },
                [null],
            ],
            [
                function (ReflectionHelper $reflectionHelper) {
                },
                ['Opulence\Ioc\ReflectionHelper'],
            ],
            [
                function (?int $first, ReflectionHelper $reflectionHelper) {
                },
                [null, 'Opulence\Ioc\ReflectionHelper'],
            ],
        ];
    }

    /**
     * @dataProvider getParameterClassNameProvider
     *
     * @param callable $closure
     * @param array $expectedResults
     * @throws ReflectionException
     */
    public function testGetParameterClassName(callable $closure, array $expectedResults)
    {
        $unresolvedParameters = (new ReflectionFunction($closure))->getParameters();

        $classNames = [];
        foreach ($unresolvedParameters as $parameter) {
            $classNames[] = ReflectionHelper::getParameterClassName($parameter);
        }

        $this->assertSame($expectedResults, $classNames);
    }

    /**
     * @return array[]
     */
    public function getParameterTypeNameProvider() : array
    {
        return [
            [
                function () {
                },
                [],
            ],
            [
                function ($a) {
                },
                [null],
            ],
            [
                function (bool $a) {
                },
                ['bool'],
            ],
            [
                function (ReflectionHelper $reflectionHelper) {
                },
                ['Opulence\Ioc\ReflectionHelper'],
            ],
            [
                function (?int $first, ReflectionHelper $reflectionHelper) {
                },
                ['int', 'Opulence\Ioc\ReflectionHelper'],
            ],
        ];
    }

    /**
     * @dataProvider getParameterTypeNameProvider
     *
     * @param callable $closure
     * @param array $expectedResults
     * @throws ReflectionException
     */
    public function testGetParameterTypeName(callable $closure, array $expectedResults)
    {
        $unresolvedParameters = (new ReflectionFunction($closure))->getParameters();

        $classNames = [];
        foreach ($unresolvedParameters as $parameter) {
            $classNames[] = ReflectionHelper::getParameterTypeName($parameter);
        }

        $this->assertSame($expectedResults, $classNames);
    }
}
