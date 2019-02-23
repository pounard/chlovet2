<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Page
{
    private $created_at;
    private $revision_at;
    private $current_revision;
    private $id;
    private $title;

    public function getId(): UuidInterface
    {
        return $this->id ?? ($this->id = Uuid::uuid4());
    }

    public function getCurrentRevision(): ?int
    {
        return $this->current_revision;
    }

    public function getCreationDate(): \DateTimeInterface
    {
        return $this->created_at ?? ($this->created_at = new \DateTimeImmutable());
    }

    public function getRevisionDate(): ?\DateTimeInterface
    {
        return $this->revision_at;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}
