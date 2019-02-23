<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Goat\Query\DeleteQuery;
use Goat\Query\InsertQueryQuery;
use Goat\Query\InsertValuesQuery;
use Goat\Query\QueryBuilder;
use Goat\Query\QueryError;
use Goat\Query\SelectQuery;
use Goat\Query\UpdateQuery;
use Goat\Query\Writer\FormatterInterface;
use Goat\Runner\EmptyResultIterator;
use Goat\Runner\ResultIterator;
use Goat\Runner\Runner;
use Goat\Runner\Transaction;
use Goat\Runner\Driver\DriverError;

/**
 * @todo
 *   Once stabilized, push into makinacorpus/goat-bundle
 *
 * Proxifies Goat queries to DoctrineMigration::addSql() method.
 */
class DoctrineMigrationRunner implements Runner
{
    private $migration;
    private $runner;

    /**
     * Default constructor
     */
    public function __construct(Runner $runner, AbstractMigration $migration)
    {
        $this->migration = $migration;
        $this->runner = $runner;
    }

    /**
     * Proxify to Doctrine addSql() method
     */
    private function addSql(string $sql, array $params = [])
    {
        // Heavy dark magic, sorry.
        \call_user_func(
            \Closure::bind(function () use ($sql, $params) {
                $this->addSql($sql, $params);
            }, $this->migration, AbstractMigration::class),
            $sql, $params
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDebug(bool $value): void
    {
        $this->runner->setDebug($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isDebugEnabled(): bool
    {
        return $this->runner->isDebugEnabled();
    }

    /**
     * {@inheritdoc}
     */
    public function execute($query, $arguments = null, $options = null): ResultIterator
    {
        $this->perform($query, $arguments, $options);

        return new EmptyResultIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function perform($query, $arguments = null, $options = null): int
    {
        $rawSQL = '';

        try {
            $prepared = $this->getFormatter()->prepare($query, $arguments);
            $rawSQL = $prepared->getQuery();
            $args = $prepared->getArguments();

            $this->addSql($rawSQL, $args);

            return 1;

        } catch (QueryError $e) {
            throw $e;
        } catch (\PDOException $e) {
            throw new DriverError($rawSQL, [], $e);
        } catch (\Exception $e) {
            throw new DriverError($rawSQL, [], $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return new class($this, $this->runner) implements QueryBuilder
        {
            private $migrationRunner, $runner;

            public function __construct(Runner $migrationRunner, Runner $runner)
            {
                $this->migrationRunner = $migrationRunner;
                $this->runner = $runner;
            }

            public function select($relation = null, ?string $alias = null): SelectQuery
            {
                return $this->runner->select($relation, $alias);
            }

            public function update($relation, ?string $alias = null): UpdateQuery
            {
                $query = new UpdateQuery($relation, $alias);
                $query->setRunner($this->migrationRunner);

                return $query;
            }

            public function insertValues($relation): InsertValuesQuery
            {
                $query = new InsertQueryQuery($relation);
                $query->setRunner($this->migrationRunner);

                return $query;
            }

            public function insertQuery($relation): InsertQueryQuery
            {
                $query = new InsertQueryQuery($relation);
                $query->setRunner($this->migrationRunner);

                return $query;
            }

            public function delete($relation, ?string $alias = null): DeleteQuery
            {
                $query = new DeleteQuery($relation, $alias);
                $query->setRunner($this->migrationRunner);

                return $query;
            }
        };
    }

    /**
     * Get SQL formatter
     */
    public function getFormatter(): FormatterInterface
    {
        return $this->runner->getFormatter();
    }

    /**
     * {@inheritdoc}
     */
    public function getDriverName(): string
    {
        return $this->runner->getDriverName();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDeferingConstraints(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isTransactionPending(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsReturning(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareQuery($query, ?string $identifier = null) : string
    {
        throw new \BadMethodCallException("Sorry, but prepared statements will automatically handled by Doctrine itself during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function executePreparedQuery(string $identifier, $arguments = null, $options = null) : ResultIterator
    {
        throw new \BadMethodCallException("Sorry, but prepared statements will automatically handled by Doctrine itself during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function startTransaction(int $isolationLevel = Transaction::REPEATABLE_READ, bool $allowPending = false): Transaction
    {
        throw new \BadMethodCallException("Sorry, but transactions will automatically handled by Doctrine itself during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function runTransaction(callable $callback, int $isolationLevel = Transaction::REPEATABLE_READ)
    {
        throw new \BadMethodCallException("Sorry, but transactions will automatically handled by Doctrine itself during migrations.");
    }
}
