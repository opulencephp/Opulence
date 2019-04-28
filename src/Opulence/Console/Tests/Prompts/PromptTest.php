<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Console\Tests\Prompts;

use InvalidArgumentException;
use Opulence\Console\Prompts\Prompt;
use Opulence\Console\Prompts\Questions\MultipleChoice;
use Opulence\Console\Prompts\Questions\Question;
use Opulence\Console\Responses\Compilers\Compiler;
use Opulence\Console\Responses\Compilers\Lexers\Lexer;
use Opulence\Console\Responses\Compilers\Parsers\Parser;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Tests\Responses\Mocks\Response;

/**
 * Tests the console prompt
 */
class PromptTest extends \PHPUnit\Framework\TestCase
{
    /** @var Response The response to use in tests */
    private $response;
    /** @var PaddingFormatter The space padding formatter to use in tests */
    private $paddingFormatter;

    /**
     * Sets up the tests
     */
    protected function setUp(): void
    {
        $this->response = new Response(new Compiler(new Lexer(), new Parser()));
        $this->paddingFormatter = new PaddingFormatter();
    }

    /**
     * Tests an answer with spaces
     */
    public function testAnsweringWithSpaces(): void
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream('  Dave  '));
        $question = new Question('Name of dev', 'unknown');
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m", $questionText);
        $this->assertEquals('Dave', $answer);
    }

    /**
     * Tests asking an indexed multiple choice question
     */
    public function testAskingIndexedMultipleChoiceQuestion(): void
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream('2'));
        $question = new MultipleChoice('Pick', ['foo', 'bar']);
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals(
            "\033[37;44m{$question->getText()}\033[39;49m" . PHP_EOL . '  1) foo' . PHP_EOL . '  2) bar' . PHP_EOL . '  > ',
            $questionText
        );
        $this->assertEquals('bar', $answer);
    }

    /**
     * Tests asking a keyed multiple choice question
     */
    public function testAskingKeyedMultipleChoiceQuestion(): void
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream('c'));
        $question = new MultipleChoice('Pick', ['a' => 'b', 'c' => 'd']);
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals(
            "\033[37;44m{$question->getText()}\033[39;49m" . PHP_EOL . '  a) b' . PHP_EOL . '  c) d' . PHP_EOL . '  > ',
            $questionText
        );
        $this->assertEquals('d', $answer);
    }

    /**
     * Tests asking a multiple choice question with custom answer line string
     */
    public function testAskingMultipleChoiceQuestionWithCustomAnswerLineString(): void
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream('1'));
        $question = new MultipleChoice('Pick', ['foo', 'bar']);
        $question->setAnswerLineString('  : ');
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals(
            "\033[37;44m{$question->getText()}\033[39;49m" . PHP_EOL . '  1) foo' . PHP_EOL . '  2) bar' . PHP_EOL . '  : ',
            $questionText
        );
        $this->assertEquals('foo', $answer);
    }

    /**
     * Tests asking a question
     */
    public function testAskingQuestion(): void
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream('Dave'));
        $question = new Question('Name of dev', 'unknown');
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m", $questionText);
        $this->assertEquals('Dave', $answer);
    }

    /**
     * Tests an empty default answer to indexed choices
     */
    public function testEmptyDefaultAnswerToIndexedChoices(): void
    {
        $triggeredException = false;
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream(' '));
        $question = new MultipleChoice('Dummy question', ['foo', 'bar']);
        ob_start();

        try {
            $prompt->ask($question, $this->response);
        } catch (InvalidArgumentException $ex) {
            $triggeredException = true;
            ob_end_clean();
        }

        $this->assertTrue($triggeredException);
    }

    /**
     * Tests an empty default answer to keyed choices
     */
    public function testEmptyDefaultAnswerToKeyedChoices(): void
    {
        $triggeredException = false;
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream(' '));
        $question = new MultipleChoice('Dummy question', ['foo' => 'bar', 'baz' => 'blah']);
        ob_start();

        try {
            $prompt->ask($question, $this->response);
        } catch (InvalidArgumentException $ex) {
            $triggeredException = true;
            ob_end_clean();
        }

        $this->assertTrue($triggeredException);
    }

    /**
     * Tests not receiving a response
     */
    public function testNotReceivingResponse(): void
    {
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream(' '));
        $question = new Question('Name of dev', 'unknown');
        ob_start();
        $answer = $prompt->ask($question, $this->response);
        $questionText = ob_get_clean();
        $this->assertEquals("\033[37;44m{$question->getText()}\033[39;49m", $questionText);
        $this->assertEquals('unknown', $answer);
    }

    /**
     * Tests setting an invalid input stream through the constructor
     */
    public function testSettingInvalidInputStreamThroughConstructor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Prompt($this->paddingFormatter, 'foo');
    }

    /**
     * Tests setting an invalid input stream through the setter
     */
    public function testSettingInvalidInputStreamThroughSetter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $prompt = new Prompt($this->paddingFormatter, $this->getInputStream('foo'));
        $prompt->setInputStream('foo');
    }

    /**
     * Gets an input stream for use in tests
     *
     * @param mixed $input The input to write to the stream
     * @return resource The input stream to use in tests
     */
    private function getInputStream($input)
    {
        $stream = fopen('php://memory', 'rb+');
        fwrite($stream, $input);
        rewind($stream);

        return $stream;
    }
}
