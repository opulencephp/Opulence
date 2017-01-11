<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Prompts\Questions;

use InvalidArgumentException;

/**
 * Tests the multiple choice question
 */
class MultipleChoiceTest extends \PHPUnit\Framework\TestCase
{
    /** @var MultipleChoice The indexed-choice question to use in tests */
    private $indexedChoiceQuestion = null;
    /** @var MultipleChoice The keyed-choice question to use in tests */
    private $keyedChoiceQuestion = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->indexedChoiceQuestion = new MultipleChoice('Dummy question', ['foo', 'bar', 'baz']);
        $this->keyedChoiceQuestion = new MultipleChoice('Dummy question', ['a' => 'b', 'c' => 'd', 'e' => 'f']);
    }

    /**
     * Tests using an out of bounds answer
     */
    public function testAnswerOutOfBounds()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->indexedChoiceQuestion->formatAnswer(4);
    }

    /**
     * Tests if the choices are associative
     */
    public function testCheckingIfChoicesAreAssociative()
    {
        $this->assertFalse($this->indexedChoiceQuestion->choicesAreAssociative());
        $this->assertTrue($this->keyedChoiceQuestion->choicesAreAssociative());
    }

    /**
     * Tests an empty answer for keyed choices
     */
    public function testEmptyAnswerForAssociativeChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->keyedChoiceQuestion->formatAnswer('');
    }

    /**
     * Tests an empty answer for indexed choices
     */
    public function testEmptyAnswerForIndexedChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->indexedChoiceQuestion->formatAnswer('');
    }

    /**
     * Tests using a float as an answer to indexed choices
     */
    public function testFloatAsAnswerToIndexedChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->indexedChoiceQuestion->formatAnswer(1.5);
    }

    /**
     * Tests formatting multiple answers
     */
    public function testFormattingMultipleAnswers()
    {
        $this->indexedChoiceQuestion->setAllowsMultipleChoices(true);
        $this->keyedChoiceQuestion->setAllowsMultipleChoices(true);
        $this->assertEquals(['foo', 'bar'], $this->indexedChoiceQuestion->formatAnswer('1,2'));
        $this->assertEquals(['d', 'f'], $this->keyedChoiceQuestion->formatAnswer('c,e'));
    }

    /**
     * Tests formatting multiple answers
     */
    public function testFormattingMultipleAnswersWithSpaces()
    {
        $this->indexedChoiceQuestion->setAllowsMultipleChoices(true);
        $this->keyedChoiceQuestion->setAllowsMultipleChoices(true);
        $this->assertEquals(['bar', 'baz'], $this->indexedChoiceQuestion->formatAnswer('2, 3'));
        $this->assertEquals(['b', 'f'], $this->keyedChoiceQuestion->formatAnswer('a, e'));
    }

    /**
     * Tests formatting a single answer
     */
    public function testFormattingSingleAnswer()
    {
        $this->assertEquals('foo', $this->indexedChoiceQuestion->formatAnswer(1));
        $this->assertEquals('bar', $this->indexedChoiceQuestion->formatAnswer(2));
        $this->assertEquals('baz', $this->indexedChoiceQuestion->formatAnswer(3));
    }

    /**
     * Tests formatting a string answer
     */
    public function testFormattingStringAnswer()
    {
        $this->assertEquals('foo', $this->indexedChoiceQuestion->formatAnswer('1'));
        $this->assertEquals('bar', $this->indexedChoiceQuestion->formatAnswer('2'));
        $this->assertEquals('baz', $this->indexedChoiceQuestion->formatAnswer('3'));
        $this->assertEquals('b', $this->keyedChoiceQuestion->formatAnswer('a'));
        $this->assertEquals('d', $this->keyedChoiceQuestion->formatAnswer('c'));
        $this->assertEquals('f', $this->keyedChoiceQuestion->formatAnswer('e'));
    }

    /**
     * Tests getting whether we allow multiple choices
     */
    public function testGettingAllowsMultipleChoices()
    {
        $this->assertFalse($this->indexedChoiceQuestion->allowsMultipleChoices());
    }

    /**
     * Tests getting the answer line string
     */
    public function testGettingAnswerLineString()
    {
        $this->indexedChoiceQuestion->setAnswerLineString(' > ');
    }

    /**
     * Tests getting the choices
     */
    public function testGettingChoices()
    {
        $this->assertEquals(['foo', 'bar', 'baz'], $this->indexedChoiceQuestion->getChoices());
        $this->assertEquals(['a' => 'b', 'c' => 'd', 'e' => 'f'], $this->keyedChoiceQuestion->getChoices());
    }

    /**
     * Tests an invalid answer for keyed choices
     */
    public function testInvalidAnswerForKeyedChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->keyedChoiceQuestion->formatAnswer('p');
    }

    /**
     * Tests selecting multiple indexed choices when it's not allowed
     */
    public function testMultipleIndexedChoicesWhenNotAllowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->indexedChoiceQuestion->setAllowsMultipleChoices(false);
        $this->indexedChoiceQuestion->formatAnswer('1,2');
    }

    /**
     * Tests selecting multiple keyed choices when it's not allowed
     */
    public function testMultipleKeyedChoicesWhenNotAllowed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->keyedChoiceQuestion->setAllowsMultipleChoices(false);
        $this->keyedChoiceQuestion->formatAnswer('a,c');
    }

    /**
     * Tests a null answer to indexed choices
     */
    public function testNullAnswerToIndexedChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->indexedChoiceQuestion->formatAnswer(null);
    }

    /**
     * Tests a null answer to keyed choices
     */
    public function testNullAnswerToKeyedChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->keyedChoiceQuestion->formatAnswer(null);
    }

    /**
     * Tests setting whether we allow multiple choices
     */
    public function testSettingAllowsMultipleChoices()
    {
        $this->indexedChoiceQuestion->setAllowsMultipleChoices(true);
        $this->assertTrue($this->indexedChoiceQuestion->allowsMultipleChoices());
    }

    /**
     * Tests setting the answer line string
     */
    public function testSettingAnswerLineString()
    {
        $this->indexedChoiceQuestion->setAnswerLineString('foo');
        $this->assertEquals('foo', $this->indexedChoiceQuestion->getAnswerLineString());
    }

    /**
     * Tests using a non-numeric string as an answer to indexed choices
     */
    public function testStringAsAnswerToIndexedChoices()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->indexedChoiceQuestion->formatAnswer('foo');
    }
}
