<?php

declare(strict_types=1);

namespace App\Repository\Impl;

use App\Repository\ClientRepository;
use Goat\Mapper\EntityManager;
use Goat\Mapper\Repository\AbstractRepository;
use Goat\Mapper\Tests\Mock\Client;
use Ramsey\Uuid\Uuid;

class DefaultClientRepository extends AbstractRepository implements ClientRepository
{
    public function __construct(EntityManager $manager)
    {
        parent::__construct(Client::class, $manager);
    }

    /**
     * {@inheritdoc}
     */
    public function upsert(string $email, ?array $values = null): Client
    {
        $insert = $this
            ->getRunner()
            ->getQueryBuilder()
            ->merge(
                $this->getRelation()
            )
            ->setKey(['email'])
            ->onConflictUpdate()
            ->values([
                'id' => Uuid::uuid4(),
                'email' => $email
            ] + $values)
        ;

        $this->addQueryReturningClause($insert);
        $this->addQueryEntityHydrator($insert);

        return $insert->execute()->fetch();
    }
}
