<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the multiple choice question
 */
namespace RDev\Console\Prompts\Questions;

class MultipleChoiceTest extends \PHPUnit_Framework_TestCase 
{
    /** @var MultipleChoice The question to use in tests */
    private $question = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->question = new MultipleChoice("Dummy question", ["foo", "bar", "baz"]);
    }

    /**
     * Tests using an out of bounds answer
     */
    public function testAnswerOutOfBounds()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->question->formatAnswer(4);
    }

    /**
     * Tests formatting multiple answers
     */
    public function testFormattingMultipleAnswers()
    {
        $this->assertEquals(["foo", "bar"], $this->question->formatAnswer("1,2"));
    }

    /**
     * Tests formatting multiple answers
     */
    public function testFormattingMultipleAnswersWithSpaces()
    {
        $this->assertEquals(["bar", "baz"], $this->question->formatAnswer("2, 3"));
    }

    /**
     * Tests formatting a single answer
     */
    public function testFormattingSingleAnswer()
    {
        $this->assertEquals("foo", $this->question->formatAnswer(1));
        $this->assertEquals("bar", $this->question->formatAnswer(2));
        $this->assertEquals("baz", $this->question->formatAnswer(3));
    }

    /**
     * Tests formatting a string answer
     */
    public function testFormattingStringAnswer()
    {
        $this->assertEquals("foo", $this->question->formatAnswer("1"));
        $this->assertEquals("bar", $this->question->formatAnswer("2"));
        $this->assertEquals("baz", $this->question->formatAnswer("3"));
    }

    /**
     * Tests getting the choices
     */
    public function testGettingChoices()
    {
        $this->assertEquals(["foo", "bar", "baz"], $this->question->getChoices());
    }

    /**
     * Tests using an invalid type answer
     */
    public function testInvalidTypeAnswer()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $this->question->formatAnswer("foo");
    }
}