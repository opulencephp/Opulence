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
    private $migrator;

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
        if ($this->optionIsSet('number')) {
            $numMigrations = (int)$this->getOptionValue('number');

            if ($numMigrations === 1) {
                $response->writeln('Rolling back last migration...');
            } else {
                $response->writeln("Rolling back last $numMigrations migrations...");
            }

            $migrationsRolledBack = $this->migrator->rollBackMigrations($numMigrations);
        } else {
            $response->writeln('Rolling back all migrations...');
            $migrationsRolledBack = $this->migrator->rollBackAllMigrations();
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
}
