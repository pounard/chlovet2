<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Goat\Converter\ConverterInterface;
use Goat\Driver\Query\AbstractSqlWriter;
use Goat\Driver\Query\SqlWriter;
use Goat\Query\QueryBuilder;
use Goat\Query\QueryError;
use Goat\Runner\AbstractRunnerProxy;
use Goat\Runner\EmptyResultIterator;
use Goat\Runner\ResultIterator;
use Goat\Runner\Runner;
use Goat\Runner\Transaction;
use Goat\Runner\Hydrator\HydratorRegistry;
use Goat\Runner\Metadata\ResultMetadataCache;

/**
 * @todo
 *   Once stabilized, push into makinacorpus/goat-bundle
 *
 * Proxifies Goat queries to DoctrineMigration::addSql() method.
 */
class DoctrineMigrationRunner extends AbstractRunnerProxy
{
    private AbstractMigration $migration;
    private Runner $runner;
    private ?SqlWriter $formatter = null;

    /**
     * Default constructor
     */
    public function __construct(Runner $runner, AbstractMigration $migration)
    {
        parent::__construct($runner);

        $this->runner = $runner;
        $this->migration = $migration;
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
            $rawSQL = $prepared->getRawSQL();
            $args = $prepared->prepareArgumentsWith($this->runner->getConverter(), $query, $arguments);

            $this->addSql($rawSQL, $args);

            return 1;
        } catch (QueryError $e) {
            throw $e;
        } catch (\PDOException $e) {
            throw new QueryError($rawSQL, [], $e);
        } catch (\Exception $e) {
            throw new QueryError($rawSQL, [], $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder(): QueryBuilder
    {
        return new DoctrineMigrationQueryBuilder($this, $this->runner);
    }

    /**
     * Get SQL formatter
     */
    public function getFormatter(): SqlWriter
    {
        if ($this->formatter) {
            return $this->formatter;
        }

        // Dark magic, again: clone the original formatter, change its formater
        // by decorating with a custom one, and set this new clone as being our
        // own formater.
        $decoratedFormater = clone $this->runner->getFormatter();
        \call_user_func(
            \Closure::bind(
                function () {
                    $this->escaper = new DoctrineMigrationEscaper($this->escaper);
                },
                $decoratedFormater,
                AbstractSqlWriter::class
            )
        );

        return $this->formatter = $decoratedFormater;
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
    public function supportsTransactionSavepoints(): bool
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
    public function prepareQuery($query, ?string $identifier = null): string
    {
        throw new \BadMethodCallException("Sorry, but prepared statements will automatically handled by Doctrine itself during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function executePreparedQuery(string $identifier, $arguments = null, $options = null): ResultIterator
    {
        throw new \BadMethodCallException("Sorry, but prepared statements will automatically handled by Doctrine itself during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function createTransaction(int $isolationLevel = Transaction::REPEATABLE_READ, bool $allowPending = true): Transaction
    {
        throw new \BadMethodCallException("Sorry, but transactions will automatically handled by Doctrine itself during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(int $isolationLevel = Transaction::REPEATABLE_READ, bool $allowPending = true): Transaction
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

    /**
     * {@inheritdoc}
     */
    public function setResultMetadataCache(ResultMetadataCache $metadataCache): void
    {
        $this->runner->setResultMetadataCache($metadataCache);
    }

    /**
     * {@inheritdoc}
     */
    public function setHydratorRegistry(HydratorRegistry $hydratorRegistry): void
    {
        throw new \BadMethodCallException("Sorry, but this is disabled during migrations.");
    }

    /**
     * {@inheritdoc}
     */
    public function setConverter(ConverterInterface $converter): void
    {
        throw new \BadMethodCallException("Sorry, but this is disabled during migrations.");
    }
}
