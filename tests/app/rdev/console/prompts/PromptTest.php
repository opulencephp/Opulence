<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the console prompt
 */
namespace RDev\Console\Prompts;
use RDev\Console\Responses\Compilers;
use RDev\Console\Responses\Compilers\Lexers;
use RDev\Console\Responses\Compilers\Parsers;
use RDev\Console\Responses\Formatters;
use RDev\Tests\Console\Responses\Mocks;

class PromptTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\Response The response to use in tests */
    private $response = null;
    /** @var Formatters\Padding The space padding formatter to use in tests */
    private $paddingFormatter  = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->response = new Mocks\Response(new Compilers\Compiler(new Lexers\Lexer(), new Parsers\Parser()));
        $this->paddingFormatter = new Formatters\Padding();
    }

    /**
     * Tests an answer with spaces
     */
    public function testAnsweringWithSpaces()
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream("  Dave  "));
        $question = new Questions\Question("Name of dev", "unknown");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m", $questionText);
        $this->assertEquals("Dave", $answer);
    }

    /**
     * Tests asking an indexed multiple choice question
     */
    public function testAskingIndexedMultipleChoiceQuestion()
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream("2"));
        $question = new Questions\MultipleChoice("Pick", ["foo", "bar"]);
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m" . PHP_EOL . "  1) foo" . PHP_EOL . "  2) bar" . PHP_EOL . "  > ", $questionText);
        $this->assertEquals("bar", $answer);
    }

    /**
     * Tests asking a keyed multiple choice question
     */
    public function testAskingKeyedMultipleChoiceQuestion()
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream("c"));
        $question = new Questions\MultipleChoice("Pick", ["a" => "b", "c" => "d"]);
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m" . PHP_EOL . "  a) b" . PHP_EOL . "  c) d" . PHP_EOL . "  > ", $questionText);
        $this->assertEquals("d", $answer);
    }

    /**
     * Tests asking a multiple choice question with custom answer line string
     */
    public function testAskingMultipleChoiceQuestionWithCustomAnswerLineString()
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream("1"));
        $question = new Questions\MultipleChoice("Pick", ["foo", "bar"]);
        $question->setAnswerLineString("  : ");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m" . PHP_EOL . "  1) foo" . PHP_EOL . "  2) bar" . PHP_EOL . "  : ", $questionText);
        $this->assertEquals("foo", $answer);
    }

    /**
     * Tests asking a question
     */
    public function testAskingQuestion()
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream("Dave"));
        $question = new Questions\Question("Name of dev", "unknown");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m", $questionText);
        $this->assertEquals("Dave", $answer);
    }

    /**
     * Tests an empty default answer to indexed choices
     */
    public function testEmptyDefaultAnswerToIndexedChoices()
    {
        $triggeredException = false;
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream(" "));
        $question = new Questions\MultipleChoice("Dummy question", ["foo", "bar"]);
        ob_start();

        try
        {
            $prompt->ask($question, $this->response);
        }
        catch(\InvalidArgumentException $ex)
        {
            $triggeredException = true;
            ob_end_clean();
        }

        $this->assertTrue($triggeredException);
    }

    /**
     * Tests an empty default answer to keyed choices
     */
    public function testEmptyDefaultAnswerToKeyedChoices()
    {
        $triggeredException = false;
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream(" "));
        $question = new Questions\MultipleChoice("Dummy question", ["foo" => "bar", "baz" => "blah"]);
        ob_start();

        try
        {
            $prompt->ask($question, $this->response);
        }
        catch(\InvalidArgumentException $ex)
        {
            $triggeredException = true;
            ob_end_clean();
        }

        $this->assertTrue($triggeredException);
    }

    /**
     * Tests not receiving a response
     */
    public function testNotReceivingResponse()
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream(" "));
        $question = new Questions\Question("Name of dev", "unknown");
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m", $questionText);
        $this->assertEquals("unknown", $answer);
    }

    /**
     * Tests setting an invalid input stream through the constructor
     */
    public function testSettingInvalidInputStreamThroughConstructor()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        new Prompt($this->paddingFormatter, "foo");
    }

    /**
     * Tests setting an invalid input stream through the setter
     */
    public function testSettingInvalidInputStreamThroughSetter()
    {
        $this->setExpectedException("\\InvalidArgumentException");
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream("foo"));
        $prompt->setInputStream("foo");
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