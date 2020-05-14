<?php

declare(strict_types=1);

namespace App\Repository\Impl;

use App\Entity\Form;
use App\Entity\FormData;
use App\Repository\FormDataRepository;
use Goat\Mapper\EntityManager;
use Goat\Mapper\Repository\AbstractRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DefaultFormDataRepository extends AbstractRepository implements FormDataRepository
{
    public function __construct(EntityManager $manager)
    {
        parent::__construct(FormData::class, $manager);
    }

    /**
     * {@inheritdoc}
     */
    public function insert(string $type, array $data, ?UuidInterface $clientId = null): FormData
    {
        if (!Form::isValid($type)) {
            throw new \InvalidArgumentException(\sprintf("Le type de formulaire '%s' n'existe pas.", $type));
        }

        $query = $this
            ->getRunner()
            ->getQueryBuilder()
            ->insert(
                $this->getRelation()
            )
            ->values([
                'client_id' => $clientId,
                'data' => \json_encode($data),
                'id' => Uuid::uuid4(),
                'type' => $type,
            ])
        ;

        $this->addQueryReturningClause($query);
        $this->addQueryEntityHydrator($query);

        return $query->execute()->fetch();
    }
}
