<?php

namespace App\Migrations;

use Goat\Query\DeleteQuery;
use Goat\Query\InsertQuery;
use Goat\Query\MergeQuery;
use Goat\Query\Query;
use Goat\Query\QueryBuilder;
use Goat\Query\SelectQuery;
use Goat\Query\UpdateQuery;
use Goat\Runner\Runner;

/**
 * Doctrine migration specific query builder.
 */
class DoctrineMigrationQueryBuilder implements QueryBuilder
{
    private DoctrineMigrationRunner $migrationRunner;
    private Runner $runner;

    public function __construct(DoctrineMigrationRunner $migrationRunner, Runner $runner)
    {
        $this->migrationRunner = $migrationRunner;
        $this->runner = $runner;
    }

    /**
     * {@inheritdoc}
     */
    public function select($relation = null, ?string $alias = null): SelectQuery
    {
        return $this->runner->select($relation, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function update($relation, ?string $alias = null): UpdateQuery
    {
        $query = new UpdateQuery($relation, $alias);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function insert($relation): InsertQuery
    {
        $query = new InsertQuery($relation);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function insertValues($relation): InsertQuery
    {
        $query = new InsertQuery($relation);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function insertQuery($relation): InsertQuery
    {
        $query = new InsertQuery($relation);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($relation, ?string $alias = null): DeleteQuery
    {
        $query = new DeleteQuery($relation, $alias);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function merge($relation): MergeQuery
    {
        $query = new MergeQuery($relation);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function upsertQuery($relation): MergeQuery
    {
        $query = new MergeQuery($relation);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function upsertValues($relation): MergeQuery
    {
        $query = new MergeQuery($relation);
        $query->setRunner($this->migrationRunner);

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(callable $callback, ?string $identifier = null): Query
    {
        throw new \BadMethodCallException("%s::prepare() is not supported during migrations", QueryBuilder::class);
    }
}
