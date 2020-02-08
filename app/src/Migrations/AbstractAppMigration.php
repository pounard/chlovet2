<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Goat\Runner\Runner;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Testing migration.
 *
 * @todo Wrap the runner to call addSql() methods instead of running them directly:
 *   - not running addSql() causes the console command to raise warnings,
 *   - SQL queries will be reported in the UI,
 *   - it will enable the dry run mode to work as expected.
 */
abstract class AbstractAppMigration extends AbstractMigration implements ContainerAwareInterface
{
    private $container;
    private $migrationRunner;
    private $runner;

    /**
     * {@inheritdoc}
     */
    final public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get container
     */
    final protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get the SQL runner that has complete and direct access over the database.
     *
     * USE THIS FOR READONLY SQL ONLY ! THIS WILL NOT BE DRY-RUNNED !
     */
    final protected function getRealRunner(): Runner
    {
        return $this->runner ?? ($this->runner = $this->container->get(Runner::class));
    }

    /**
     * Get Goat runner
     */
    final protected function getRunner(): Runner
    {
        return $this->migrationRunner ?? ($this->migrationRunner = new DoctrineMigrationRunner($this->getRealRunner(), $this));
    }

    /**
     * {@inheritdoc}
     */
    final public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException("For now, going down is disabled, sorry.");
    }
}
