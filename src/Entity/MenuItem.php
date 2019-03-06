<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

final class MenuItem
{
    use MenuTrait;

    private $parent_id;
    private $route;
    private $slug;
    private $title;
    private $weight;

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
