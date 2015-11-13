<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Prompts\Questions;

use InvalidArgumentException;

/**
 * Defines a multiple choice question
 */
class MultipleChoice extends Question
{
    /** @var array The list of choices */
    private $choices = [];
    /** @var string The string to display before the input */
    private $answerLineString = "  > ";
    /** @var bool Whether or not to allow multiple choices */
    private $allowsMultipleChoices = false;

    /*
     * @param string $question The question text
     * @param array $choices The list of choices
     * @param mixed $defaultResponse The default answer to the question
     */
    public function __construct($question, array $choices, $defaultAnswer = null)
    {
        parent::__construct($question, $defaultAnswer);

        $this->choices = $choices;
    }

    /**
     * @return bool
     */
    public function allowsMultipleChoices()
    {
        return $this->allowsMultipleChoices;
    }

    /**
     * Gets whether or not the choices are an associative array
     *
     * @return bool True if the array is associative, otherwise false
     */
    public function choicesAreAssociative()
    {
        return (bool)count(array_filter(array_keys($this->choices), "is_string"));
    }

    /**
     * @inheritdoc
     */
    public function formatAnswer($answer)
    {
        $hasMultipleAnswers = false;
        $answer = str_replace(" ", "", $answer);

        if (mb_strpos($answer, ",") === false) {
            // The answer is not a list of answers
            $answers = [$answer];
        } else {
            if (!$this->allowsMultipleChoices) {
                throw new InvalidArgumentException("Multiple choices are not allowed");
            }

            $hasMultipleAnswers = true;
            $answers = explode(",", $answer);
        }

        if ($this->choicesAreAssociative()) {
            $selectedChoices = $this->getSelectedAssociativeChoices($answers);
        } else {
            $selectedChoices = $this->getSelectedIndexChoices($answers);
        }

        if (count($selectedChoices) == 0) {
            throw new InvalidArgumentException("Invalid choice");
        }

        if ($hasMultipleAnswers) {
            return $selectedChoices;
        } else {
            return $selectedChoices[0];
        }
    }

    /**
     * @return string
     */
    public function getAnswerLineString()
    {
        return $this->answerLineString;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param bool $allowsMultipleChoices
     */
    public function setAllowsMultipleChoices($allowsMultipleChoices)
    {
        $this->allowsMultipleChoices = $allowsMultipleChoices;
    }

    /**
     * @param string $answerLineString
     */
    public function setAnswerLineString($answerLineString)
    {
        $this->answerLineString = $answerLineString;
    }

    /**
     * Gets the list of selected associative choices from a list of answers
     *
     * @param array $answers The list of answers
     * @return array The list of selected choices
     */
    private function getSelectedAssociativeChoices(array $answers)
    {
        $selectedChoices = [];

        foreach ($answers as $answer) {
            if (array_key_exists($answer, $this->choices)) {
                $selectedChoices[] = $this->choices[$answer];
            }
        }

        return $selectedChoices;
    }

    /**
     * Gets the list of selected indexed choices from a list of answers
     *
     * @param array $answers The list of answers
     * @return array The list of selected choices
     * @throws InvalidArgumentException Thrown if the answers are not of the correct type
     */
    private function getSelectedIndexChoices(array $answers)
    {
        $selectedChoices = [];

        foreach ($answers as $answer) {
            if (!ctype_digit($answer)) {
                throw new InvalidArgumentException("Answer is not an integer");
            }

            $answer = (int)$answer;

            if ($answer < 1 || $answer > count($this->choices)) {
                throw new InvalidArgumentException("Choice must be between 1 and " . count($this->choices));
            }

            // Answers are 1-indexed
            $selectedChoices[] = $this->choices[$answer - 1];
        }

        return $selectedChoices;
    }
}