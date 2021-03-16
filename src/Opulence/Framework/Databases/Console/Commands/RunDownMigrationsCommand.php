<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Databases\Console\Commands;

use Exception;
use Opulence\Console\Commands\Command;
use Opulence\Console\Requests\Option;
use Opulence\Console\Requests\OptionTypes;
use Opulence\Console\Responses\IResponse;
use Opulence\Databases\Migrations\IMigrator;

/**
 * Defines the command that runs the "down" migrations
 */
class RunDownMigrationsCommand extends Command
{
    /** @var IMigrator The migrator to use */
    private $migrator = null;

    /**
     * @param IMigrator $migrator The migrator to use
     */
    public function __construct(IMigrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('migrations:down')
            ->setDescription('Runs the "down" database migrations')
            ->addOption(new Option(
                'number',
                null,
                OptionTypes::REQUIRED_VALUE,
                'The number of migrations to roll back',
                1
            ));
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        try {
            if ($this->optionIsSet('number')) {
                $numMigrations = (int)$this->getOptionValue('number');

                $migrationsRolledBack = $this->doExecuteSome($response, $numMigrations);
            } else {
                $migrationsRolledBack = $this->doExecuteAll($response);
            }

            $this->writeResults($response, $migrationsRolledBack);
        } catch (Exception $e) {
            $this->writeException($response, $e);
        }
    }

    /**
     * @param IResponse $response
     * @param int       $numMigrations
     *
     * @return array|string[]
     */
    protected function doExecuteSome(IResponse $response, int $numMigrations) : array
    {
        if ($numMigrations === 1) {
            $response->writeln('Rolling back last migration...');
        } else {
            $response->writeln("Rolling back last $numMigrations migrations...");
        }

        $migrationsRolledBack = $this->migrator->rollBackMigrations($numMigrations);

        return $migrationsRolledBack;
    }

    /**
     * @param IResponse $response
     *
     * @return array|string[]
     */
    protected function doExecuteAll(IResponse $response) : array
    {
        $response->writeln('Rolling back all migrations...');
        $migrationsRolledBack = $this->migrator->rollBackAllMigrations();

        return $migrationsRolledBack;
    }

    /**
     * @param IResponse $response
     * @param Exception $exc
     */
    protected function writeException(IResponse $response, Exception $exc)
    {
        $response->writeln(sprintf('<fatal>%s</fatal>', $exc->getMessage()));
    }

    /**
     * @param IResponse $response
     * @param array     $migrationsRolledBack
     */
    protected function writeResults(IResponse $response, array $migrationsRolledBack)
    {
        if (count($migrationsRolledBack) === 0) {
            $response->writeln('<info>No migrations to roll back</info>');
        } else {
            $response->writeln('<success>Successfully rolled back the following migrations:</success>');

            foreach ($migrationsRolledBack as $migration) {
                $response->writeln("<info>$migration</info>");
            }
        }
    }
}
