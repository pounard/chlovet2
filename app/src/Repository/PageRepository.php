<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Page;
use App\Entity\PageRevision;
use Goat\Query\QueryBuilder;
use Goat\Query\Value;
use Goat\Runner\QueryPagerResultIterator;
use Goat\Runner\Runner;
use Ramsey\Uuid\UuidInterface;

final class PageRepository
{
    private Runner $runner;

    /**
     * Default constructor
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * {@internal}
     */
    public function getRunner(): Runner
    {
        return $this->runner;
    }

    /**
     * Fetch basic information
     */
    public function info(UuidInterface $id): ?Page
    {
        return $this
            ->runner
            ->getQueryBuilder()
            ->select('public.page', 'p')
            ->columns(['p.*'])
            ->condition('p.id', $id)
            ->execute([], Page::class)
            ->fetch()
        ;
    }

    /**
     * Fetch current revision
     */
    public function current(UuidInterface $id): ?PageRevision
    {
        return $this
            ->runner
            ->getQueryBuilder()
            ->select('public.page_revision', 'pr')
            ->columns(['pr.*', 'page_at' => 'p.created_at'])
            ->join('public.page', 'p.id = pr.id and pr.revision = p.current_revision', 'p')
            ->condition('pr.id', $id)
            ->execute([], PageRevision::class)
            ->fetch()
        ;
    }

    /**
     * Create new page
     */
    public function create(): ?Page
    {
        return $this
            ->runner
            ->getQueryBuilder()
            ->insertValues('public.page')
            ->returning()
            ->execute([], Page::class)
            ->fetch()
        ;
    }

    /**
     * @return QueryPagerResultIterator|\App\Entity\PageRevision[]
     */
    public function find($criteria): QueryPagerResultIterator
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * @return QueryPagerResultIterator|\App\Entity\PageRevision[]
     */
    public function revisions(UuidInterface $id): QueryPagerResultIterator
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * Append a new revision
     */
    public function append(UuidInterface $id, string $title, $data): PageRevision
    {
        return $this->runner->runTransaction(
            function (QueryBuilder $builder) use ($id, $title, $data) {

                $nextRevisionId = ((int)$builder
                    ->select('public.page_revision')
                    ->columnExpression('max(revision)')
                    ->condition('id', $id)
                    ->execute()
                    ->fetchField()
                ) + 1;

                $ret = $builder
                    ->insertValues('public.page_revision')
                    ->values([
                        'created_at' => (new \DateTime()),
                        'data' => new Value($data, 'json'),
                        'id' => $id,
                        'revision' => $nextRevisionId,
                        'title' => $title,
                    ])
                    ->returning()
                    ->execute([], PageRevision::class)
                    ->fetch()
                ;

                $builder
                    ->update('page')
                    ->set('current_revision', $nextRevisionId)
                    ->condition('id', $id)
                    ->execute()
                ;

                return $ret;
            }
        );
    }

    /**
     * Set current revision to given revision number
     */
    public function setCurrentRevision(UuidInterface $id, int $revision): ?PageRevision
    {
        throw new \Exception("Not implemented yet");
    }

    /**
     * Delete a complete page, along all revisions
     */
    public function delete(UuidInterface $id): int
    {
        $queryBuilder = $this->runner->getQueryBuilder();

        $queryBuilder
            ->delete('public.page_revision')
            ->condition('id', $id)
            ->execute()
        ;

        return $queryBuilder
            ->delete('public.page')
            ->condition('id', $id)
            ->execute()
            ->countRows()
        ;
    }

    /**
     * Delete a single revision
     */
    public function deleteRevision(UuidInterface $id, int $revision): ?PageRevision
    {
        return $this
            ->runner
            ->getQueryBuilder()
            ->delete('public.page_revision')
            ->condition('id', $id)
            ->condition('revision', $revision)
            ->returning()
            ->execute([], PageRevision::class)
            ->fetch()
        ;
    }
}
