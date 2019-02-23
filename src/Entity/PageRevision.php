<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;

final class PageRevision
{
    private $created_at;
    private $data;
    private $id;
    private $page_at;
    private $revision;
    private $title;

    public function create(UuidInterface $pageId): self
    {
        $ret = new self;
        $ret->data = [];
        $ret->id = $pageId;

        return $ret;
    }

    public function getPageId(): UuidInterface
    {
        return $this->id;
    }

    public function getRevision(): ?int
    {
        return $this->revision;
    }

    public function getCreationDate(): \DateTimeInterface
    {
        return $this->created_at ?? ($this->created_at = new \DateTimeImmutable());
    }

    /**
     * It might not be loaded (partial object load).
     */
    public function getPageCreationDate(): ?\DateTimeInterface
    {
        return $this->page_at;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }
}
