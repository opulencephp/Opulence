<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines a basic response
 */
namespace RDev\Console\Responses;
use RDev\Console\Responses\Formatters\Elements;

abstract class Response implements IResponse
{
    /** @var Compilers\ICompiler The response compiler to use */
    protected $compiler = null;
    /** @var Elements\ElementRegistry The element registry */
    protected $elementRegistry = [];

    /**
     * @param Compilers\ICompiler $compiler The response compiler to use
     */
    public function __construct(Compilers\ICompiler $compiler)
    {
        $this->compiler = $compiler;
        // Register the built-in elements
        $this->elementRegistry = new Elements\ElementRegistry();
        $this->elementRegistry->registerElement(new Elements\Element("info", new Elements\Style("green")));
        $this->elementRegistry->registerElement(new Elements\Element("error", new Elements\Style("black", "yellow")));
        $this->elementRegistry->registerElement(new Elements\Element("fatal", new Elements\Style("white", "red")));
        $this->elementRegistry->registerElement(new Elements\Element("question", new Elements\Style("white", "blue")));
        $this->elementRegistry->registerElement(new Elements\Element("comment", new Elements\Style("yellow")));
        $this->elementRegistry->registerElement(new Elements\Element("b", new Elements\Style(null, null, ["bold"])));
        $this->elementRegistry->registerElement(new Elements\Element("u", new Elements\Style(null, null, ["underline"])));
    }

    /**
     * {@inheritdoc}
     */
    public function getElementRegistry()
    {
        return $this->elementRegistry;
    }

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
            $this->doWrite($this->compiler->compile($message, $this->elementRegistry), false);
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
            $this->doWrite($this->compiler->compile($message, $this->elementRegistry), true);
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