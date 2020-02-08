<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

final class MenuItem
{
    use MenuTrait;

    private ?int $parent_id = null;
    private /* string */ $route;
    private /* string */ $slug;
    private ?string $title = null;
    private int $weight = 0;

    private function __construct()
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPageId(): ?UuidInterface
    {
        return $this->page_id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getWeight(): int
    {
        return $this->weight ?? 0;
    }
}
