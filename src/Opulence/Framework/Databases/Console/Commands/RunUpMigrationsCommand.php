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
use Opulence\Console\Responses\IResponse;
use Opulence\Databases\Migrations\IMigrator;

/**
 * Defines the command that runs the "up" migrations
 */
final class RunUpMigrationsCommand extends Command
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
        $this->setName('migrations:up')
            ->setDescription('Runs the "up" database migrations');
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $response->writeln('Running "up" migrations...');

        try {
            $migrationsRun = $this->migrator->runMigrations();
        } catch (Exception $e) {
            $this->writeException($response, $e);

            return;
        }

        if (count($migrationsRun) === 0) {
            $response->writeln('<info>No migrations to run</info>');
        } else {
            $response->writeln('<success>Successfully ran the following migrations:</success>');

            foreach ($migrationsRun as $migrationRun) {
                $response->writeln("<info>$migrationRun</info>");
            }
        }
    }

    /**
     * Writes the exception to output
     *
     * @param IResponse $response The response to write to
     * @param Exception $ex The exception that was thrown
     */
    protected function writeException(IResponse $response, Exception $ex): void
    {
        $response->writeln(sprintf('<fatal>%s</fatal>', $ex->getMessage()));
    }
}
