<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
final class RunDownMigrationsCommand extends Command
{
    /** @var IMigrator The migrator to use */
    private IMigrator $migrator;

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
    protected function define(): void
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
        $migrationsRolledBack = [];

        try {
            if ($this->optionIsSet('number')) {
                $numMigrations = (int)$this->getOptionValue('number');
                $migrationsRolledBack = $this->doExecuteSome($response, $numMigrations);
            } else {
                $migrationsRolledBack = $this->doExecuteAll($response);
            }

            $this->writeResults($response, $migrationsRolledBack);
        } catch (Exception $ex) {
            $this->writeException($response, $ex);
        }

        if (count($migrationsRolledBack) === 0) {
            $response->writeln('<info>No migrations to roll back</info>');
        } else {
            $response->writeln('<success>Successfully rolled back the following migrations:</success>');

            foreach ($migrationsRolledBack as $migration) {
                $response->writeln("<info>$migration</info>");
            }
        }
    }

    /**
     * Executes all migrations
     *
     * @param IResponse $response The response to write to
     * @return array The list of migrations rolled back
     * @throws Exception Thrown if there was an error rolling back migrations
     */
    protected function doExecuteAll(IResponse $response): array
    {
        $response->writeln('Rolling back all migrations...');

        return $this->migrator->rollBackAllMigrations();
    }

    /**
     * Executes some number of migrations
     *
     * @param IResponse $response The response to write to
     * @param int $numMigrations The number of migrations to run
     * @return array The list of migrations rolled back
     * @throws Exception Thrown if there was an error rolling back migrations
     */
    protected function doExecuteSome(IResponse $response, int $numMigrations): array
    {
        if ($numMigrations === 1) {
            $response->writeln('Rolling back last migration...');
        } else {
            $response->writeln("Rolling back last $numMigrations migrations...");
        }

        return $this->migrator->rollBackMigrations($numMigrations);
    }

    /**
     * Writes the exception to the response
     *
     * @param IResponse $response
     * @param Exception $ex
     */
    protected function writeException(IResponse $response, Exception $ex): void
    {
        $response->writeln(sprintf('<fatal>%s</fatal>', $ex->getMessage()));
    }

    /**
     * Writes the results of the migration back to the output
     *
     * @param IResponse $response The response to write to
     * @param array $migrationsRolledBack The list of migrations rolled back
     */
    protected function writeResults(IResponse $response, array $migrationsRolledBack): void
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
