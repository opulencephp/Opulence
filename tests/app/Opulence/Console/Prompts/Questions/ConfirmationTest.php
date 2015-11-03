<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Prompts\Questions;

/**
 * Tests the confirmation question
 */
class ConfirmationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Confirmation The question to use in tests */
    private $question = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->question = new Confirmation("Is Dave cool (yn)");
    }

    /**
     * Tests formatting false values
     */
    public function testFormattingFalseValues()
    {
        $this->assertFalse($this->question->formatAnswer("n"));
        $this->assertFalse($this->question->formatAnswer("N"));
        $this->assertFalse($this->question->formatAnswer("no"));
        $this->assertFalse($this->question->formatAnswer("NO"));
    }

    /**
     * Tests formatting true values
     */
    public function testFormattingTrueValues()
    {
        $this->assertTrue($this->question->formatAnswer("y"));
        $this->assertTrue($this->question->formatAnswer("Y"));
        $this->assertTrue($this->question->formatAnswer("yes"));
        $this->assertTrue($this->question->formatAnswer("YES"));
    }
}