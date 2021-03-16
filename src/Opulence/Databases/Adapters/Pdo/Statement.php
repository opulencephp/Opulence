<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Databases\Adapters\Pdo;

use Opulence\Databases\IStatement;
use PDO;
use PDOStatement;

/**
 * Defines an extension of PDOStatement
 */
class Statement implements IStatement
{
    /** @var PDOStatement */
    protected $pdoStatement;

    /**
     * We need this because PDO is expecting a private/protected constructor in PDOStatement
     */
    protected function __construct()
    {
        $this->pdoStatement = new PDOStatement();
    }

    /**
     * @inheritdoc
     */
    public function bindParam($parameter, &$variable, $dataType = PDO::PARAM_STR, $length = null, $driverOptions = null)
    {
        return $this->pdoStatement->bindParam($parameter, $variable, $dataType, $length, $driverOptions);
    }

    /**
     * @inheritdoc
     */
    public function bindValue($parameter, $value, $dataType = PDO::PARAM_STR)
    {
        return $this->pdoStatement->bindValue($parameter, $value, $dataType);
    }

    /**
     * Binds a list of values to the statement
     *
     * @param array $values The mapping of parameter name to a value or to an array
     *      If mapping to an array, the first item should be the value and the second should be the data type constant
     * @return bool True if successful, otherwise false
     */
    public function bindValues(array $values)
    {
        $isAssociativeArray = count(array_filter(array_keys($values), 'is_string')) > 0;

        foreach ($values as $parameterName => $value) {
            if (!is_array($value)) {
                $value = [$value, PDO::PARAM_STR];
            }

            // If this is an indexed array, we need to offset the parameter name by 1 because it's 1-indexed
            if (!$isAssociativeArray) {
                ++$parameterName;
            }

            if (count($value) !== 2 || !$this->bindValue($parameterName, $value[0], $value[1])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function execute($parameters = null)
    {
        return $this->pdoStatement->execute($parameters);
    }

    /**
     * @inheritdoc
     */
    public function fetch($fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE, $cursorOrientation = null, $cursorOffset = null)
    {
        if ($fetchStyle === null && $cursorOrientation === null && $cursorOffset === null) {
            return $this->pdoStatement->fetch();
        }

        if ($cursorOrientation === null && $cursorOffset === null) {
            return $this->pdoStatement->fetch($fetchStyle);
        }

        if ($cursorOffset === null) {
            return $this->pdoStatement->fetch($fetchStyle, $cursorOrientation);
        }

        return $this->pdoStatement->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($fetchStyle = PDO::ATTR_DEFAULT_FETCH_MODE, $fetchArgument = null, $ctorArgs = null)
    {
        if ($fetchStyle === null && $fetchArgument === null && $ctorArgs === null) {
            return $this->pdoStatement->fetchAll();
        }

        if ($fetchArgument === null && $ctorArgs === null) {
            return $this->pdoStatement->fetchAll($fetchStyle);
        }

        if ($ctorArgs === null) {
            return $this->pdoStatement->fetchAll($fetchStyle, $fetchArgument);
        }

        return $this->pdoStatement->fetchAll($fetchStyle, $fetchArgument, $ctorArgs);
    }

    /**
     * @inheritdoc
     */
    public function fetchColumn($columnNumber = 0)
    {
        return $this->pdoStatement->fetchColumn($columnNumber);
    }

    /**
     * @inheritdoc
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        if ($arg2 === null && $arg3 === null) {
            return $this->pdoStatement->setFetchMode($fetchMode);
        }

        if ($arg3 === null) {
            return $this->pdoStatement->setFetchMode($fetchMode, $arg2);
        }

        return $this->pdoStatement->setFetchMode($fetchMode, $arg2, $arg3);
    }

    /**
     * @inheritdoc
     */
    public function closeCursor()
    {
        return $this->pdoStatement->closeCursor();
    }

    /**
     * @inheritdoc
     */
    public function columnCount()
    {
        return $this->pdoStatement->columnCount();
    }

    /**
     * @inheritdoc
     */
    public function errorCode()
    {
        return $this->pdoStatement->errorCode();
    }

    /**
     * @inheritdoc
     */
    public function errorInfo()
    {
        return $this->pdoStatement->errorInfo();
    }

    /**
     * @inheritdoc
     */
    public function rowCount()
    {
        return $this->pdoStatement->rowCount();
    }
}
