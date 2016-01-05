<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Prompts\Questions;

use InvalidArgumentException;

/**
 * Defines the interface for questions to implement
 */
interface IQuestion
{
    /**
     * Formats an answer
     * Useful for subclasses to override
     *
     * @param mixed $answer The answer to format
     * @return mixed The formatted answer
     * @throws InvalidArgumentException Thrown if the answer is not of the correct type
     */
    public function formatAnswer($answer);

    /**
     * Gets the default answer
     *
     * @return mixed The default answer
     */
    public function getDefaultAnswer();

    /**
     * Gets the question text
     *
     * @return string The question text
     */
    public function getText();
}