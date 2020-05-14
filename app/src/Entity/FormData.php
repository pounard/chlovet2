<?php

declare(strict_types=1);

namespace App\Entity;

use Goat\Mapper\Definition\Builder\DefinitionBuilder;
use Goat\Mapper\Definition\Registry\StaticEntityDefinition;
use Ramsey\Uuid\UuidInterface;

final class FormData implements StaticEntityDefinition
{
    private UuidInterface $id;
    private ?UuidInterface $clientId = null;
    private string $type;
    private array $data;
    private \DateTimeInterface $createdAt;
    private ?\DateTimeInterface $sentAt;

    /**
     * {@inheritdoc}
     */
    public static function defineEntity(DefinitionBuilder $builder): void
    {
        $builder->setTableName('form_data');
        $builder->setPrimaryKey(['id' => 'uuid']);
        $builder->addProperty('id');
        $builder->addProperty('clientId', 'client_id');
        $builder->addProperty('type');
        $builder->addProperty('createdAt', 'created_at');
        $builder->addProperty('sentAt', 'sent_at');
        $builder->addProperty('data');
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getClientId(): ?UuidInterface
    {
        return $this->clientId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }
}
