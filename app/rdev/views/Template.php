<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines methods common to all website page files
 */
namespace RDev\Views;

class Template implements ITemplate
{
    /** The default open tag for unescaped delimiter  */
    const DEFAULT_OPEN_UNESCAPED_TAG_DELIMITER = "{{!";
    /** The default close tag for unescaped delimiter  */
    const DEFAULT_CLOSE_UNESCAPED_TAG_DELIMITER = "!}}";
    /** The default open tag for escaped delimiter  */
    const DEFAULT_OPEN_ESCAPED_TAG_DELIMITER = "{{";
    /** The default close tag for escaped delimiter */
    const DEFAULT_CLOSE_ESCAPED_TAG_DELIMITER = "}}";
    /** The default open tag for statement delimiter  */
    const DEFAULT_OPEN_STATEMENT_DELIMITER = "{%";
    /** The default close tag for statement delimiter */
    const DEFAULT_CLOSE_STATEMENT_DELIMITER = "%}";

    /** @var string The uncompiled contents of the template */
    protected $contents = "";
    /** @var array The mapping of tag names to their values */
    protected $tags = [];
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var array The mapping of template part names to their contents */
    protected $parts = [];
    /** @var ITemplate|null The parent template if there is one, otherwise false */
    protected $parent;
    /** @var array The stack of parent delimiter types to values */
    private $delimiters = [
        self::DELIMITER_TYPE_UNESCAPED_TAG => [
            self::DEFAULT_OPEN_UNESCAPED_TAG_DELIMITER,
            self::DEFAULT_CLOSE_UNESCAPED_TAG_DELIMITER
        ],
        self::DELIMITER_TYPE_ESCAPED_TAG => [
            self::DEFAULT_OPEN_ESCAPED_TAG_DELIMITER,
            self::DEFAULT_CLOSE_ESCAPED_TAG_DELIMITER
        ],
        self::DELIMITER_TYPE_STATEMENT => [
            self::DEFAULT_OPEN_STATEMENT_DELIMITER,
            self::DEFAULT_CLOSE_STATEMENT_DELIMITER
        ]
    ];

    /**
     * @param string $contents The contents of the template
     */
    public function __construct($contents = "")
    {
        $this->setContents($contents);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelimiters($type)
    {
        if(!isset($this->delimiters[$type]))
        {
            return [null, null];
        }

        return $this->delimiters[$type];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getPart($name)
    {
        if(isset($this->parts[$name]))
        {
            return $this->parts[$name];
        }
        elseif($this->parent !== null)
        {
            return $this->parent->getPart($name);
        }

        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function getParts()
    {
        $parts = $this->parts;
        $currParent = $this->parent;

        while($currParent !== null)
        {
            foreach($this->parent->getParts() as $name => $content)
            {
                if(!array_key_exists($name, $this->parts))
                {
                    $parts[$name] = $content;
                }
            }

            $currParent = $this->parent->getParent();
        }

        return $parts;
    }

    /**
     * {@inheritdoc}
     */
    public function getTag($name)
    {
        if(isset($this->tags[$name]))
        {
            return $this->tags[$name];
        }
        elseif($this->parent !== null)
        {
            return $this->parent->getTag($name);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTags()
    {
        $tags = $this->tags;
        $currParent = $this->parent;

        while($currParent !== null)
        {
            foreach($this->parent->getTags() as $name => $value)
            {
                if(!array_key_exists($name, $this->tags))
                {
                    $tags[$name] = $value;
                }
            }

            $currParent = $this->parent->getParent();
        }

        return $tags;
    }

    /**
     * {@inheritdoc}
     */
    public function getVar($name)
    {
        if(isset($this->vars[$name]))
        {
            return $this->vars[$name];
        }
        elseif($this->parent !== null)
        {
            return $this->parent->getVar($name);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getVars()
    {
        $vars = $this->vars;
        $currParent = $this->parent;

        while($currParent !== null)
        {
            foreach($this->parent->getVars() as $name => $value)
            {
                if(!array_key_exists($name, $this->vars))
                {
                    $vars[$name] = $value;
                }
            }

            $currParent = $this->parent->getParent();
        }

        return $vars;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function setContents($contents)
    {
        if(!is_string($contents))
        {
            throw new \InvalidArgumentException("Contents are not a string");
        }

        $this->contents = $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function setDelimiters($type, array $values)
    {
        $this->delimiters[$type] = $values;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(ITemplate $parent)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function setPart($name, $content)
    {
        $this->parts[$name] = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function setParts(array $namesToContents)
    {
        foreach($namesToContents as $name => $content)
        {
            $this->setPart($name, $content);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTag($name, $value)
    {
        $this->tags[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setTags(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setTag($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVar($name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function setVars(array $namesToValues)
    {
        foreach($namesToValues as $name => $value)
        {
            $this->setVar($name, $value);
        }
    }
} 