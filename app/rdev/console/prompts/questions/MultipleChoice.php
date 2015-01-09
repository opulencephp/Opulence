<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a multiple choice question
 */
namespace RDev\Console\Prompts\Questions;

class MultipleChoice extends Question
{
    /** @var array The list of choices */
    private $choices = [];
    /** @var string The string to display before the input */
    private $answerLineString = " > ";
    /** @var bool Whether or not to allow multiple choices */
    private $allowsMultipleChoices = false;

    /*
     * @param string $question The question text
     * @param array $choices The list of choices
     * @param mixed $defaultResponse The default value for the response
     */
    public function __construct($question, array $choices, $defaultResponse = null)
    {
        parent::__construct($question, $defaultResponse);

        $this->choices = $choices;
    }

    /**
     * @return mixed
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
     * {@inheritdoc}
     */
    public function formatAnswer($answer)
    {
        $hasMultipleAnswers = false;
        $answer = str_replace(" ", "", $answer);

        if(strpos($answer, ",") === false)
        {
            $answers = [$answer];
        }
        else
        {
            if(!$this->allowsMultipleChoices)
            {
                throw new \InvalidArgumentException("Multiple choices are not allowed");
            }

            $hasMultipleAnswers = true;
            $answers = explode(",", $answer);
        }

        if($this->choicesAreAssociative())
        {
            $selectedChoices = $this->formatForAssociativeChoices($answers);
        }
        else
        {
            $selectedChoices = $this->formatForIndexedChoices($answers);
        }

        if(count($selectedChoices) == 0)
        {
            throw new \InvalidArgumentException("Invalid choice");
        }

        if($hasMultipleAnswers)
        {
            return $selectedChoices;
        }
        else
        {
            return $selectedChoices[0];
        }
    }

    /**
     * @return mixed
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
     * @param mixed $allowsMultipleChoices
     */
    public function setAllowsMultipleChoices($allowsMultipleChoices)
    {
        $this->allowsMultipleChoices = $allowsMultipleChoices;
    }

    /**
     * @param mixed $answerLineString
     */
    public function setAnswerLineString($answerLineString)
    {
        $this->answerLineString = $answerLineString;
    }

    /**
     * Formats a list of answers for an associative list of choices
     *
     * @param array $answers The list of answers
     * @return array The list of selected choices
     */
    private function formatForAssociativeChoices(array $answers)
    {
        $selectedChoices = [];

        foreach($answers as $answer)
        {
            if(array_key_exists($answer, $this->choices))
            {
                $selectedChoices[] = $this->choices[$answer];
            }
        }

        return $selectedChoices;
    }

    /**
     * Formats a list of answers for an indexed list of choices
     *
     * @param array $answers The list of answers
     * @return array The list of selected choices
     * @throws \InvalidArgumentException Thrown if the answers are not of the correct type
     */
    private function formatForIndexedChoices(array $answers)
    {
        $selectedChoices = [];

        foreach($answers as $answer)
        {
            if(!is_numeric($answer))
            {
			    throw new \InvalidArgumentException("Answer is not numeric");
            }

            $answer = (int)$answer;

            if($answer < 1 || $answer > count($this->choices))
            {
                throw new \InvalidArgumentException("Choice is outside bounds");
            }

            // Answers are 1-indexed
            $selectedChoices[] = $this->choices[$answer - 1];
        }

        return $selectedChoices;
    }
}