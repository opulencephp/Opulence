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
 * Defines the command handler that runs the "up" migrations
 */
final class RunUpMigrationsCommandHandler implements ICommandHandler
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
        $output->writeln('Running "up" migrations...');

        try {
            $migrationsRun = $this->migrator->runMigrations();
        } catch (Exception $e) {
            $this->writeException($output, $e);

            return;
        }

        if (count($migrationsRun) === 0) {
            $output->writeln('<info>No migrations to run</info>');
        } else {
            $output->writeln('<success>Successfully ran the following migrations:</success>');

            foreach ($migrationsRun as $migrationRun) {
                $output->writeln("<info>$migrationRun</info>");
            }
        }
    }

    /**
     * Writes the exception to output
     *
     * @param IOutput $output The output to write to
     * @param Exception $ex The exception that was thrown
     */
    protected function writeException(IOutput $output, Exception $ex): void
    {
        $output->writeln(sprintf('<fatal>%s</fatal>', $ex->getMessage()));
    }
}
