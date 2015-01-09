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

    /*
     * {@inheritdoc}
     * @param array $choices The list of choices
     */
    public function __construct($question, array $choices, $defaultResponse = null)
    {
        parent::__construct($question, $defaultResponse);

        $this->choices = $choices;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAnswer($answer)
    {
        $hasMultipleAnswers = false;

        if(strpos($answer, ",") !== false)
        {
            $hasMultipleAnswers = true;
            $answers = explode(",", $answer);
        }
        else
        {
            $answers = [$answer];
        }

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

            // The answer is 1-indexed
            $selectedChoices[] = $this->choices[$answer - 1];
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
}