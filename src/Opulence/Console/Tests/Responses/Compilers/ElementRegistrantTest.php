<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
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
    public function setUp()
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
        $compiler->expects($this->at(0))
            ->method('registerElement')
            ->with('success', new Style(Colors::BLACK, Colors::GREEN));
        $compiler->expects($this->at(1))
            ->method('registerElement')
            ->with('info', new Style(Colors::GREEN));
        $compiler->expects($this->at(2))
            ->method('registerElement')
            ->with('error', new Style(Colors::BLACK, Colors::YELLOW));
        $compiler->expects($this->at(3))
            ->method('registerElement')
            ->with('fatal', new Style(Colors::WHITE, Colors::RED));
        $compiler->expects($this->at(4))
            ->method('registerElement')
            ->with('question', new Style(Colors::WHITE, Colors::BLUE));
        $compiler->expects($this->at(5))
            ->method('registerElement')
            ->with('comment', new Style(Colors::YELLOW));
        $compiler->expects($this->at(6))
            ->method('registerElement')
            ->with('b', new Style(null, null, [TextStyles::BOLD]));
        $compiler->expects($this->at(7))
            ->method('registerElement')
            ->with('u', new Style(null, null, [TextStyles::UNDERLINE]));
        $this->registrant->registerElements($compiler);
    }
}
