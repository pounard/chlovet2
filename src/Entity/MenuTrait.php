<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

/**
 * This class is actually just a pure hack that allows the tree and tree items
 * classes to use the properties from one another, for better encapsulation.
 */
trait MenuTrait
{
    private $children = [];
    private $id;
    private $page_id;
    private $sorted = false;

    private function sortChildren(): void
    {
        if ($this->children) {
            \uasort($this->children, function (MenuItem $a, MenuItem $b) {
                return $a->getWeight() - $b->getWeight();
            });
        }
    }

    public function isInTrailOfPage(UuidInterface $pageId): bool
    {
        if (isset($this->page_id) && $pageId === $this->page_id) {
            return true;
        }
        if ($this->children) {
            foreach ($this->children as $child) {
                if ($child->isInTrailOfPage($pageId)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function isInTrailOfItem(int $id): bool
    {
        if (isset($this->id) && $id === $this->id) {
            return true;
        }
        if ($this->children) {
            foreach ($this->children as $child) {
                if ($child->isInTrailOfItem($id)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren(): iterable
    {
        if (!$this->sorted) {
            $this->sortChildren();
            $this->sorted = true;
        }

        return $this->children;
    }

    /**
     * Has this object children
     */
    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * Count the number of children
     */
    public function getChildCount(): int
    {
        return \count($this->children);
    }
}
