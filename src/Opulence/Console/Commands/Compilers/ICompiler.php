<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Console\Commands\Compilers;

use Opulence\Console\Commands\ICommand;
use Opulence\Console\Requests\IRequest;
use RuntimeException;

/**
 * Defines the interface for command compilers to implement
 */
interface ICompiler
{
    /**
     * Compiles a command using request data
     *
     * @param ICommand $command The command to compile
     * @param IRequest $request The request from the user
     * @return ICommand The compiled command
     * @throws RuntimeException Thrown if there was an error compiling the command
     */
    public function compile(ICommand $command, IRequest $request) : ICommand;
}