<?php

declare(strict_types=1);

namespace App\Entity;

use Goat\Mapper\Definition\Builder\DefinitionBuilder;
use Goat\Mapper\Definition\Registry\StaticEntityDefinition;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Client implements StaticEntityDefinition
{
    private ?UuidInterface $id = null;
    private string $email;
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
        $relation = $builder->addOneToManyRelation('formulaires', FormData::class);
        $relation->setTargetKey(['client_id' => 'uuid']);
    }

    public function getId(): UuidInterface
    {
        return $this->id ?? ($this->id = Uuid::uuid4());
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return FormData[]
     */
    public function getFormulaires(): ?iterable
    {
        return $this->formulaires;
    }
}
