<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views;

/**
 * Defines a basic view
 */
class View implements IView
{
    /** The default open tag for unsanitized delimiter  */
    const DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER = "{{!";
    /** The default close tag for unsanitized delimiter  */
    const DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER = "!}}";
    /** The default open tag for sanitized delimiter  */
    const DEFAULT_OPEN_SANITIZED_TAG_DELIMITER = "{{";
    /** The default close tag for sanitized delimiter */
    const DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER = "}}";
    /** The default open tag for directive delimiter  */
    const DEFAULT_OPEN_DIRECTIVE_DELIMITER = "<%";
    /** The default close tag for directive delimiter */
    const DEFAULT_CLOSE_DIRECTIVE_DELIMITER = "%>";
    /** The default open tag for comment delimiter  */
    const DEFAULT_OPEN_COMMENT_DELIMITER = "{#";
    /** The default close tag for comment delimiter */
    const DEFAULT_CLOSE_COMMENT_DELIMITER = "#}";

    /** @var string The uncompiled contents of the view */
    protected $contents = "";
    /** @var string The path to the raw view */
    protected $path = "";
    /** @var array The mapping of PHP variable names to their values */
    protected $vars = [];
    /** @var array The stack of parent delimiter types to values */
    private $delimiters = [
        self::DELIMITER_TYPE_UNSANITIZED_TAG => [
            self::DEFAULT_OPEN_UNSANITIZED_TAG_DELIMITER,
            self::DEFAULT_CLOSE_UNSANITIZED_TAG_DELIMITER
        ],
        self::DELIMITER_TYPE_SANITIZED_TAG => [
            self::DEFAULT_OPEN_SANITIZED_TAG_DELIMITER,
            self::DEFAULT_CLOSE_SANITIZED_TAG_DELIMITER
        ],
        self::DELIMITER_TYPE_DIRECTIVE => [
            self::DEFAULT_OPEN_DIRECTIVE_DELIMITER,
            self::DEFAULT_CLOSE_DIRECTIVE_DELIMITER
        ],
        self::DELIMITER_TYPE_COMMENT => [
            self::DEFAULT_OPEN_COMMENT_DELIMITER,
            self::DEFAULT_CLOSE_COMMENT_DELIMITER
        ]
    ];

    /**
     * @param string $path The path to the raw view
     * @param string $contents The contents of the view
     */
    public function __construct(string $path = "", string $contents = "")
    {
        $this->setPath($path);
        $this->setContents($contents);
    }

    /**
     * @inheritdoc
     */
    public function getContents() : string
    {
        return $this->contents;
    }

    /**
     * @inheritdoc
     */
    public function getDelimiters($type) : array
    {
        if (!isset($this->delimiters[$type])) {
            return [null, null];
        }

        return $this->delimiters[$type];
    }

    /**
     * @inheritdoc
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getVar(string $name)
    {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getVars() : array
    {
        return $this->vars;
    }

    /**
     * @inheritdoc
     */
    public function hasVar(string $name) : bool
    {
        return array_key_exists($name, $this->vars);
    }

    /**
     * @inheritdoc
     */
    public function setContents(string $contents)
    {
        $this->contents = $contents;
    }

    /**
     * @inheritdoc
     */
    public function setDelimiters($type, array $values)
    {
        $this->delimiters[$type] = $values;
    }

    /**
     * @inheritdoc
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @inheritdoc
     */
    public function setVar(string $name, $value)
    {
        $this->vars[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function setVars(array $namesToValues)
    {
        foreach ($namesToValues as $name => $value) {
            $this->setVar($name, $value);
        }
    }
}