<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Responses\Compilers;

use Opulence\Console\Responses\Compilers\ElementRegistrant;
use Opulence\Console\Responses\Compilers\Elements\Colors;
use Opulence\Console\Responses\Compilers\Elements\Style;
use Opulence\Console\Responses\Compilers\Elements\TextStyles;
use Opulence\Console\Responses\Compilers\ICompiler;

/**
 * Tests the element registrant
 */
class ElementRegistrantTest extends \PHPUnit\Framework\TestCase
{
    /** @var ElementRegistrant The registrant to use in tests */
    private $registrant = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->registrant = new ElementRegistrant();
    }

    /**
     * Tests that the correct elements are registered
     */
    public function testCorrectElementsAreRegistered()
    {
        /** @var ICompiler|\PHPUnit_Framework_MockObject_MockObject $compiler */
        $compiler = $this->createMock(ICompiler::class);
        $compiler->expects($this->exactly(8))
            ->method('registerElement')
            ->withConsecutive(
                ['success', new Style(Colors::BLACK, Colors::GREEN)],
                ['info', new Style(Colors::GREEN)],
                ['error', new Style(Colors::BLACK, Colors::YELLOW)],
                ['fatal', new Style(Colors::WHITE, Colors::RED)],
                ['question', new Style(Colors::WHITE, Colors::BLUE)],
                ['comment', new Style(Colors::YELLOW)],
                ['b', new Style(null, null, [TextStyles::BOLD])],
                ['u', new Style(null, null, [TextStyles::UNDERLINE])],
            );
        $this->registrant->registerElements($compiler);
    }
}
