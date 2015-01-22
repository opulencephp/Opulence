<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines an element compiler
 */
namespace RDev\Console\Responses\Compilers;
use RDev\Console\Responses\Formatters\Elements;

class Compiler implements ICompiler
{
    /**
     * {@inheritdoc}
     */
    public function compile($message, Elements\ElementRegistry $elementRegistry)
    {
        $output = "";
        $outputBuffer = "";
        $elementNameBuffer = "";
        /** @var Elements\Element[] $elementStack */
        $elementStack = [];
        $messageLength = strlen($message);
        $inOpenTag = false;
        $inCloseTag = false;

        try
        {
            for($charIter = 0;$charIter < $messageLength;$charIter++)
            {
                $char = $message[$charIter];

                switch($char)
                {
                    case "<":
                        if($this->lookBehind($message, $charIter) == "\\")
                        {
                            // This tag was escaped
                            $outputBuffer .= $char;
                        }
                        else
                        {

                            // Check if this is a closing tag
                            if($this->peek($message, $charIter) == "/")
                            {
                                $inCloseTag = true;
                                $inOpenTag = false;
                            }
                            else
                            {
                                $inCloseTag = false;
                                $inOpenTag = true;

                                // Check if there are any styles to apply
                                if(count($elementStack) > 0)
                                {
                                    $outputBuffer = $this->applyElementStyles($elementStack, $outputBuffer);
                                }

                                // Flush the output buffer
                                $output .= $outputBuffer;
                                $outputBuffer = "";
                            }
                        }

                        break;
                    case ">";
                        if($inOpenTag || $inCloseTag)
                        {
                            if($inOpenTag)
                            {
                                $elementStack[] = $elementRegistry->getElement($elementNameBuffer);
                            }
                            else
                            {
                                // Apply the styles to this buffer
                                $outputBuffer = $this->applyElementStyles($elementStack, $outputBuffer);
                                $output .= $outputBuffer;
                                $outputBuffer = "";
                                $poppedElement = array_pop($elementStack);

                                // Force proper nesting
                                if(count($elementStack) > 0 && $poppedElement->getName() !== $elementNameBuffer)
                                {
                                    throw new \RuntimeException(
                                        sprintf(
                                            "Incorrect nesting of %s and %s tags",
                                            $poppedElement->getName(),
                                            $elementNameBuffer
                                        )
                                    );
                                }
                            }

                            $elementNameBuffer = "";
                            $inOpenTag = false;
                            $inCloseTag = false;
                        }
                        else
                        {
                            $outputBuffer .= $char;
                        }

                        break;
                    default:
                        if($inOpenTag || $inCloseTag)
                        {
                            // We're in a tag, so buffer the element name
                            if($char != "/")
                            {
                                $elementNameBuffer .= $char;
                            }
                        }
                        else
                        {
                            // We're outside of a tag somewhere
                            $outputBuffer .= $char;
                        }

                        break;
                }
            }

            if(count($elementStack) > 0)
            {
                $names = [];

                foreach($elementStack as $element)
                {
                    $names[] = $element->getName();
                }

                throw new \RuntimeException("Unclosed element tags: " . implode(", ", $names));
            }
            elseif($inOpenTag || $inCloseTag)
            {
                throw new \RuntimeException("Unfinished " . ($inOpenTag ? "open" : "close") . " tag");
            }

            // Finish flushing the output buffer
            $output .= $outputBuffer;
            // Remove any escape characters
            $output = str_replace("\\<", "<", $output);

            return $output;
        }
        catch(\InvalidArgumentException $ex)
        {
            throw new \RuntimeException($ex->getMessage());
        }
    }

    /**
     * Applies the a stack of elements' styles to the input text
     *
     * @param Elements\Element[] $elementStack The stack of elements whose styles we're applying
     * @param string $text The text to format
     * @return string The formatted text
     */
    private function applyElementStyles(array $elementStack, $text)
    {
        if(strlen($text) == 0)
        {
            return "";
        }

        $formattedText = $text;

        // Loop backwards through the stack and apply each element's style
        /** @var Elements\Element $element */
        foreach(array_reverse($elementStack) as $element)
        {
            $formattedText = $element->getStyle()->format($formattedText);
        }

        return $formattedText;
    }

    /**
     * Looks back at the previous character in the string
     *
     * @param string $message The message to look behind in
     * @param int $currPosition The current position
     * @return string|null The previous character if there is one, otherwise null
     */
    private function lookBehind($message, $currPosition)
    {
        if(strlen($message) == 0 || $currPosition  == 0)
        {
            return null;
        }

        return $message[$currPosition - 1];
    }

    /**
     * Peeks at the next character in the string
     *
     * @param string $message The message to peek
     * @param int $currPosition The current position
     * @return string|null The next character if there is one, otherwise null
     */
    private function peek($message, $currPosition)
    {
        if(strlen($message) == 0 || strlen($message) == $currPosition + 1)
        {
            return null;
        }

        return $message[$currPosition + 1];
    }
}