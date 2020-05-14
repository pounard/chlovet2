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
     * Set FILLFACTOR for a table
     */
    final protected function setFillfactor(string $table, int $fillfactor): void
    {
        // Define extra space left between rows, instead of 100% as default fillfactor.
        $this->addSql(\sprintf('alter table "%s" SET (FILLFACTOR = %d);', $table, $fillfactor));

        // Rewrite the full table storage with this extra space.
        // Migration are executed during a transaction, PgSQL will raise errors:
        //   SQLSTATE[25001]: Active sql transaction: 7 ERROR:  VACUUM cannot run inside a transaction block
        // I am disabling this until I get @rle to review this.
        if (false) {
            $this->addSql(\sprintf('VACUUM FULL "%s";', $table));
        }
    }

    /**
     * Set VACCUUM parameters for a table
     */
    final protected function setVaccuum(string $table, int $analyseThresold, float $analysePercent, int $vacuumThresold, float $vaccuumPercent): void
    {
        $this->addSql(\sprintf('alter table "%s" SET (autovacuum_analyze_threshold = %d);', $table, $analyseThresold));
        $this->addSql(\sprintf('alter table "%s" SET (autovacuum_analyze_scale_factor = %f);', $table, $analysePercent));
        $this->addSql(\sprintf('alter table "%s" SET (autovacuum_vacuum_threshold = %d);', $table, $vacuumThresold));
        $this->addSql(\sprintf('alter table "%s" SET (autovacuum_vacuum_scale_factor = %f);', $table, $vaccuumPercent));

        // Right time to run one.
        // Migration are executed during a transaction, PgSQL will raise errors:
        //   SQLSTATE[25001]: Active sql transaction: 7 ERROR:  VACUUM cannot run inside a transaction block
        // I am disabling this until I get @rle to review this.
        if (false) {
            $this->addSql(\sprintf('VACUUM ANALYZE "%s";', $table));
        }
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
