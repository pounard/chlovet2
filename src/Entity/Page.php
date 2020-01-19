<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Page
{
    private \DateTimeInterface $created_at;
    private ?\DateTimeInterface $revision_at = null;
    private ?int $current_revision = null;
    private UuidInterface $id;
    private ?string $title = null;

    private function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCurrentRevision(): ?int
    {
        return $this->current_revision;
    }

    public function getCreationDate(): \DateTimeInterface
    {
        return $this->created_at;
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
