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

use Aphiria\Console\Commands\ICommandHandler;
use Aphiria\Console\Input\Input;
use Aphiria\Console\Output\IOutput;
use Exception;
use Opulence\Databases\Migrations\IMigrator;

/**
 * Defines the command handler that runs the "down" migrations
 */
final class RunDownMigrationsCommandHandler implements ICommandHandler
{
    /** @var IMigrator The migrator to use */
    private IMigrator $migrator;

    /**
     * @param IMigrator $migrator The migrator to use
     */
    public function __construct(IMigrator $migrator)
    {
        $this->migrator = $migrator;
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, IOutput $output)
    {
        $migrationsRolledBack = [];

        try {
            if (array_key_exists('number', $input->options)) {
                $numMigrations = (int)$input->options['number'];
                $migrationsRolledBack = $this->doExecuteSome($output, $numMigrations);
            } else {
                $migrationsRolledBack = $this->doExecuteAll($output);
            }

            $this->writeResults($output, $migrationsRolledBack);
        } catch (Exception $ex) {
            $this->writeException($output, $ex);
        }

        if (count($migrationsRolledBack) === 0) {
            $output->writeln('<info>No migrations to roll back</info>');
        } else {
            $output->writeln('<success>Successfully rolled back the following migrations:</success>');

            foreach ($migrationsRolledBack as $migration) {
                $output->writeln("<info>$migration</info>");
            }
        }
    }

    /**
     * Executes all migrations
     *
     * @param IOutput $output The output to write to
     * @return array The list of migrations rolled back
     * @throws Exception Thrown if there was an error rolling back migrations
     */
    protected function doExecuteAll(IOutput $output): array
    {
        $output->writeln('Rolling back all migrations...');

        return $this->migrator->rollBackAllMigrations();
    }

    /**
     * Executes some number of migrations
     *
     * @param IOutput $output The output to write to
     * @param int $numMigrations The number of migrations to run
     * @return array The list of migrations rolled back
     * @throws Exception Thrown if there was an error rolling back migrations
     */
    protected function doExecuteSome(IOutput $output, int $numMigrations): array
    {
        if ($numMigrations === 1) {
            $output->writeln('Rolling back last migration...');
        } else {
            $output->writeln("Rolling back last $numMigrations migrations...");
        }

        return $this->migrator->rollBackMigrations($numMigrations);
    }

    /**
     * Writes the exception to the response
     *
     * @param IOutput $output The output to write to
     * @param Exception $ex The exception to write
     */
    protected function writeException(IOutput $output, Exception $ex): void
    {
        $output->writeln(sprintf('<fatal>%s</fatal>', $ex->getMessage()));
    }

    /**
     * Writes the results of the migration back to the output
     *
     * @param IOutput $output The output to write to
     * @param array $migrationsRolledBack The list of migrations rolled back
     */
    protected function writeResults(IOutput $output, array $migrationsRolledBack): void
    {
        if (count($migrationsRolledBack) === 0) {
            $output->writeln('<info>No migrations to roll back</info>');
        } else {
            $output->writeln('<success>Successfully rolled back the following migrations:</success>');

            foreach ($migrationsRolledBack as $migration) {
                $output->writeln("<info>$migration</info>");
            }
        }
    }
}
