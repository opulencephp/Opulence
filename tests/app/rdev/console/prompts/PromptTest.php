<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console prompt
 */
namespace RDev\Console\Prompts;
use RDev\Tests\Console\Responses\Mocks;

class PromptTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Response The response to use in tests */
    private $response = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Mocks\Response();
    }

    /**
     * Tests an answer with spaces
     */
    public function testAnsweringWithSpaces()
    {
        $prompt = new Prompt($this->getInputStream("  Dave  "));
        $question = new Questions\Question("Name of dev", "unknown");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals($question->getText(), $questionText);
        $this->assertEquals("Dave", $answer);
    }

    /**
     * Tests asking an indexed multiple choice question
     */
    public function testAskingIndexedMultipleChoiceQuestion()
    {
        $prompt = new Prompt($this->getInputStream("2"));
        $question = new Questions\MultipleChoice("Pick", ["foo", "bar"]);
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("Pick" . PHP_EOL . " 1) foo" . PHP_EOL . " 2) bar" . PHP_EOL . " > ", $questionText);
        $this->assertEquals("bar", $answer);
    }

    /**
     * Tests asking a keyed multiple choice question
     */
    public function testAskingKeyedMultipleChoiceQuestion()
    {
        $prompt = new Prompt($this->getInputStream("c"));
        $question = new Questions\MultipleChoice("Pick", ["a" => "b", "c" => "d"]);
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("Pick" . PHP_EOL . " a) b" . PHP_EOL . " c) d" . PHP_EOL . " > ", $questionText);
        $this->assertEquals("d", $answer);
    }

    /**
     * Tests asking a multiple choice question with custom answer line string
     */
    public function testAskingMultipleChoiceQuestionWithCustomAnswerLineString()
    {
        $prompt = new Prompt($this->getInputStream("1"));
        $question = new Questions\MultipleChoice("Pick", ["foo", "bar"]);
        $question->setAnswerLineString(" : ");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("Pick" . PHP_EOL . " 1) foo" . PHP_EOL . " 2) bar" . PHP_EOL . " : ", $questionText);
        $this->assertEquals("foo", $answer);
    }

    /**
     * Tests asking a question
     */
    public function testAskingQuestion()
    {
        $prompt = new Prompt($this->getInputStream("Dave"));
        $question = new Questions\Question("Name of dev", "unknown");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals($question->getText(), $questionText);
        $this->assertEquals("Dave", $answer);
    }

    /**
     * Tests not receiving a response
     */
    public function testNotReceivingResponse()
    {
        $prompt = new Prompt($this->getInputStream(" "));
        $question = new Questions\Question("Name of dev", "unknown");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals($question->getText(), $questionText);
        $this->assertEquals("unknown", $answer);
    }

    /**
     * Gets an input stream for use in tests
     *
     * @param mixed $input The input to write to the stream
     * @return resource The input stream to use in tests
     */
    private function getInputStream($input)
    {
        $stream = fopen("php://memory", "r+");
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}