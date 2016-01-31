<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Composer;

use Opulence\Bootstrappers\Paths;

/**
 * Defines a wrapper for the Composer executable
 */
class Executable
{
    /** @var string The executable */
    private $executable = "";

    /**
     * @param Paths $paths The paths of the application
     */
    public function __construct(Paths $paths)
    {
        if (file_exists($paths["root"] . "/composer.phar")) {
            $this->executable = '"' . PHP_BINARY . '" composer.phar';
        } else {
            $this->executable = "composer";
        }
    }

    /**
     * Performs a dump-autoload
     *
     * @param string $options The options to run
     * @return string The output of the autoload
     */
    public function dumpAutoload(string $options = "") : string
    {
        return $this->execute("{$this->executable} dump-autoload $options");
    }

    /**
     * Performs an update
     *
     * @param string $options The options to run
     * @return string The output of the update
     */
    public function update(string $options = "") : string
    {
        return $this->execute("{$this->executable} update $options");
    }

    /**
     * Executes a command
     *
     * @param string $command The command to execute
     * @return string The output of the command
     */
    protected function execute(string $command) : string
    {
        return shell_exec($command);
    }
}