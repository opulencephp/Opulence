<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Debug\Exceptions\Handlers;

use InvalidArgumentException;
use Opulence\Console\Responses\IResponse;

/**
 * Defines the console exception handler
 */
class ExceptionRenderer implements IExceptionRenderer
{
    /** @var IResponse The response to write to */
    protected $response = null;

    /**
     * @inheritDoc
     */
    public function render($ex)
    {
        if ($ex instanceof InvalidArgumentException) {
            $this->response->writeln("<error>{$ex->getMessage()}</error>");
        } else {
            $this->response->writeln("<fatal>{$ex->getMessage()}</fatal>");
        }
    }

    /**
     * @inheritDoc
     */
    public function setResponse(IResponse $response)
    {
        $this->response = $response;
    }
}