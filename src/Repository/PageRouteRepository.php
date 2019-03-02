<?php

declare(strict_types=1);

namespace App\Repository;

use Goat\Runner\Runner;
use Ramsey\Uuid\UuidInterface;

/**
 * Beware that almost every move semantic in this class will result in more than
 * one SQL query: handling transactions properly must be done at the controller
 * or handler level.
 */
final class PageRouteRepository
{
    private $runner;

    /**
     * Default constructor
     */
    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    /**
     * Validate item exits, and return its parent id and weight
     */
    private function validateItem(int $id): array
    {
        $values = $this->runner->execute("select parent_id, weight from public.page_route where id = ?", [$id])->fetch();

        if (!$values) {
            throw new \InvalidArgumentException(sprintf("Item %d does not exist", $id));
        }

        return \array_values($values);
    }

    /**
     * Get maximum weight under the given item, if none given, consider root
     */
    private function getMaxWeight(?int $id = null): int
    {
        if ($id) {
            $this->validateItem($id);

            return (int)$this->runner
                ->execute("select max(weight) + 1 from public.page_route where parent_id = ?", [$id])
                ->fetchField()
            ;
        }
        return (int)$this->runner
            ->execute("select max(weight) + 1 from public.page_route where parent_id is null")
            ->fetchField()
        ;
    }

    /**
     * Decal all siblings before the given one, return the item weight
     */
    private function moveSiblingsBefore(int $id): int
    {
        list($parentId, $weight) = $this->validateItem($id);

        if ($parentId) {
            $this->runner->execute(
                "update public.page_route set weight = weight - 2 where parent_id = ? and id <> ? and weight <= ?",
                [$parentId, $id, $weight - 1]
            );
        } else {
            $this->runner->execute(
                "update public.page_route set weight = weight - 2 where parent_id is null and id <> ? and weight <= ?",
                [$id, $weight - 1]
            );
        }

        return [$parentId, $weight];
    }

    /**
     * Decal all siblings after the given one, return the item parent id and weight
     */
    private function moveSiblingsAfter(int $id): array
    {
        list($parentId, $weight) = $this->validateItem($id);

        if ($parentId) {
            $this->runner->execute(
                "update public.page_route set weight = weight + 2 where parent_id = ? and id <> ? and weight >= ?",
                [$parentId, $id, $weight]
            );
        } else {
            $this->runner->execute(
                "update public.page_route set weight = weight + 2 where parent_id is null and id <> ? and weight >= ?",
                [$id, $weight]
            );
        }

        return [$parentId, $weight];
    }

    /**
     * Move item at the given position
     */
    private function moveItem(int $id, ?int $parentId = null, ?int $weight = null): void
    {
        $weight = $weight ?? $this->getMaxWeight($parentId);
        if ($parentId) {
            $this->runner->execute(
                "update public.page_route set parent_id = ?, weight = ? where id = ?",
                [$parentId, $weight, $id]
            );
        } else {
            $this->runner->execute(
                "update public.page_route set parent_id = null, weight = ? where id = ?",
                [$weight, $id]
            );
        }
    }

    /**
     * Compute route for element
     */
    private function computeRoute(string $slug, ?int $parentId = null): string
    {
        if ($parentId) {
            $parentRoute = $this
                ->runner
                ->execute("select route from public.page_route where id = ?", [$parentId])
                ->fetchField()
            ;

            if (!$parentRoute) {
                throw new \InvalidArgumentException(sprintf("Item %d does not exist", $parentId));
            }

            return \sprintf("%s/%s", $parentRoute, $slug);
        }

        return $slug;
    }

    /**
     * Insert item at the given position
     */
    private function insertItem(UuidInterface $pageId, string $slug, ?string $title = null, ?int $parentId = null, ?int $weight = null): int
    {
        return (int)$this
            ->runner
            ->getQueryBuilder()
            ->insertValues('page_route')
            ->values([
                'page_id' => $pageId,
                'parent_id' => $parentId,
                'title' => $title,
                'route' => $this->computeRoute($slug, $parentId),
                'slug' => $slug,
                'weight' => $weight ?? $this->getMaxWeight($parentId),
            ])
            ->returning('id')
            ->execute()
            ->fetchField()
        ;
    }

    /**
     * 
     */
    public function findForPage()
    {
        
    }

    /**
     * Does the item exists
     */
    public function exists(int $id): bool
    {
        return $this->runner->execute("select true from page_route where id = ?", [$id])->fetchField();
    }

    /**
     * Delete item, will fail if it has children
     */
    public function delete(int $id)
    {
        $this->runner->execute("delete from page_route where id = ?", [$id]);
    }

    /**
     * Insert new item as root at the last position
     */
    public function insert(UuidInterface $pageId, string $slug, string $title): int
    {
        return $this->insertItem($pageId, $slug, $title);
    }

    /**
     * Insert new item as child of given one at the last position
     */
    public function insertAsChild(int $otherId, UuidInterface $pageId, string $slug, string $title): int
    {
        $this->validateItem($otherId);

        return $this->insertItem($pageId, $slug, $title, $otherId);
    }

    /**
     * Insert new item after the given one
     */
    public function insertAfter(int $otherId, UuidInterface $pageId, string $slug, string $title): int
    {
        list($parentId, $weight) = $this->moveSiblingsAfter($otherId);

        return $this->insertItem($pageId, $slug, $title, $parentId, $weight + 1);
    }

    /**
     * Insert new item before the given one
     */
    public function insertBefore(int $otherId, UuidInterface $pageId, string $slug, string $title): int
    {
        list($parentId, $weight) = $this->moveSiblingsBefore($otherId);

        return $this->insertItem($pageId, $slug, $title, $parentId, $weight - 1);
    }

    /**
     * Update item information
     */
    public function update(int $id, ?string $slug = null, ?string $title = null)
    {
        $this->validateItem($id);

        $values = [];

        if (empty($values)) {
            return;
        }

        $this
            ->runner
            ->getQueryBuilder()
            ->update('page_route')
            ->sets($values)
            ->condition('id', $id)
            ->execute()
        ;
    }

    /**
     * Move item as child of the given one at last position
     */
    public function moveAsChild(int $id, int $otherId)
    {
        $this->validateItem($id);
        $this->moveItem($id, $otherId);
    }

    /**
     * Move item to root at last position
     */
    public function moveToRoot(int $id)
    {
        $this->validateItem($id);
        $this->moveItem($id);
    }

    /**
     * Move item after the given one
     */
    public function moveAfter(int $id, int $otherId)
    {
        $this->validateItem($id);
        list($parentId, $weight) = $this->moveSiblingsAfter($otherId);
        $this->moveItem($id, $parentId, $weight + 1);
    }

    /**
     * Move item before the given one
     */
    public function moveBefore(int $id, int $otherId)
    {
        $this->validateItem($id);
        list($parentId, $weight) = $this->moveSiblingsBefore($otherId);
        $this->moveItem($id, $parentId, $weight - 1);
    }
}
