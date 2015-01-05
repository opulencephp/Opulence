<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a basic output
 */
namespace RDev\Console\Output;

abstract class Output implements IOutput
{
    /**
     * {@inheritdoc}
     */
    public function write($messages)
    {
        if(!is_array($messages))
        {
            $messages = [$messages];
        }

        foreach($messages as $message)
        {
            $this->doWrite($message, false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeln($messages)
    {
        if(!is_array($messages))
        {
            $messages = [$messages];
        }

        foreach($messages as $message)
        {
            $this->doWrite($message, true);
        }
    }

    /**
     * Actually performs the writing
     *
     * @param string $message The message to write
     * @param bool $includeNewLine True if we are to include a new line character at the end of the message
     */
    abstract protected function doWrite($message, $includeNewLine);
}