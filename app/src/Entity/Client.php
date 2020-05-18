<?php

declare(strict_types=1);

namespace App\Entity;

use Goat\Mapper\Definition\Builder\DefinitionBuilder;
use Goat\Mapper\Definition\Registry\StaticEntityDefinition;
use Ramsey\Uuid\UuidInterface;

final class Client implements StaticEntityDefinition
{
    private UuidInterface $id;
    private string $email;
    private string $createdAt;
    private ?string $contactNom = null;
    private ?string $contactPrenom = null;
    private ?iterable $formulaires = null;

    /**
     * {@inheritdoc}
     */
    public static function defineEntity(DefinitionBuilder $builder): void
    {
        $builder->setTableName('client');
        $builder->setPrimaryKey(['id' => 'uuid']);
        $builder->addProperty('id');
        $builder->addProperty('email');
        $builder->addProperty('createdAt', 'created_at');
        $builder->addProperty('contactNom', 'contact_nom');
        $builder->addProperty('createdPrenom', 'contact_prenom');
        $relation = $builder->addOneToManyRelation('formulaires', FormData::class);
        $relation->setTargetKey(['client_id' => 'uuid']);
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getContactNom(): ?string
    {
        return $this->contactNom;
    }

    public function getContactPrenom(): ?string
    {
        return $this->contactPrenom;
    }

    /**
     * @return FormData[]
     */
    public function getFormulaires(): ?iterable
    {
        return $this->formulaires;
    }
}
