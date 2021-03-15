<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Console\Tests\Prompts\Questions;

use Opulence\Console\Prompts\Questions\Question;

/**
 * Tests the console prompt question
 */
class QuestionTest extends \PHPUnit\Framework\TestCase
{
    /** @var Question The question to use in tests */
    private $question = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->question = new Question('Dummy question', 'foo');
    }

    /**
     * Tests formatting the answer
     */
    public function testFormattingAnswer()
    {
        $this->assertEquals('foo', $this->question->formatAnswer('foo'));
    }

    /**
     * Tests getting the default response
     */
    public function testGettingDefaultResponse()
    {
        $this->assertEquals('foo', $this->question->getDefaultAnswer());
    }

    /**
     * Tests getting the question
     */
    public function testGettingQuestion()
    {
        $this->assertEquals('Dummy question', $this->question->getText());
    }
}
