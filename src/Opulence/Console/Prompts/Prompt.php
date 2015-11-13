<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Prompts;

use InvalidArgumentException;
use Opulence\Console\Prompts\Questions\IQuestion;
use Opulence\Console\Prompts\Questions\MultipleChoice;
use Opulence\Console\Responses\Formatters\PaddingFormatter;
use Opulence\Console\Responses\IResponse;
use RuntimeException;

/**
 * Defines a console prompt
 */
class Prompt
{
    /** @var PaddingFormatter The space padding formatter to use */
    private $paddingFormatter = null;
    /** @var resource The input stream to look for answers in */
    private $inputStream = null;

    /***
     * @param PaddingFormatter $paddingFormatter The space padding formatter to use
     * @param resource|null $inputStream The input stream to look for answers in
     */
    public function __construct(PaddingFormatter $paddingFormatter, $inputStream = null)
    {
        $this->paddingFormatter = $paddingFormatter;

        if ($inputStream === null) {
            $inputStream = STDIN;
        }

        $this->setInputStream($inputStream);
    }

    /**
     * Prompts the user to answer a question
     *
     * @param IQuestion $question The question to ask
     * @param IResponse $response The response to write output to
     * @return mixed The user's answer to the question
     * @throws RuntimeException Thrown if we failed to get the user's answer
     */
    public function ask(IQuestion $question, IResponse $response)
    {
        $response->write("<question>{$question->getText()}</question>");

        if ($question instanceof MultipleChoice) {
            /** @var MultipleChoice $question */
            $response->writeln("");
            $choicesAreAssociative = $question->choicesAreAssociative();
            $choiceTexts = [];

            foreach ($question->getChoices() as $key => $choice) {
                if (!$choicesAreAssociative) {
                    // Make the choice 1-indexed
                    $key += 1;
                }

                $choiceTexts[] = [$key . ")", $choice];
            }

            $response->writeln($this->paddingFormatter->format($choiceTexts, function ($row) {
                return "  {$row[0]} {$row[1]}";
            }));
            $response->write($question->getAnswerLineString());
        }

        $answer = fgets($this->inputStream, 4096);

        if ($answer === false) {
            throw new RuntimeException("Failed to get answer");
        }

        $answer = trim($answer);

        if (mb_strlen($answer) == 0) {
            $answer = $question->getDefaultAnswer();
        }

        return $question->formatAnswer($answer);
    }

    /**
     * Sets the input stream
     *
     * @param resource $inputStream The input stream to look for answers in
     * @throws InvalidArgumentException Thrown if the input stream is not a resource
     */
    public function setInputStream($inputStream)
    {
        if (!is_resource($inputStream)) {
            throw new InvalidArgumentException("Input stream must be resource");
        }

        $this->inputStream = $inputStream;
    }
}