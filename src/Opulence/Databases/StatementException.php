<?php

namespace Opulence\Databases;

use Exception;
use PDOStatement;
use Throwable;

class StatementException extends Exception
{
    /** @var PDOStatement */
    protected $statement;

    /**
     * StatementException constructor.
     *
     * @param PDOStatement   $statement
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(PDOStatement $statement, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->statement = $statement;

        $message = $message ?: $statement->errorInfo()[2];

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return PDOStatement
     */
    public function getStatement(): PDOStatement
    {
        return $this->statement;
    }

    /**
     * @see https://www.php.net/manual/en/pdostatement.errorinfo.php
     *
     * @return array
     */
    public function getErrorInfo(): array
    {
        return $this->statement->errorInfo();
    }

    /**
     * @see https://www.php.net/manual/en/pdostatement.errorcode.php
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->statement->errorCode();
    }
}
