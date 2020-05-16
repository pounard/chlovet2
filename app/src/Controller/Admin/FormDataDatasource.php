<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\FormData;
use Goat\Mapper\EntityManager;
use Goat\Query\Query as DbQuery;
use MakinaCorpus\Calista\Datasource\AbstractDatasource;
use MakinaCorpus\Calista\Datasource\DatasourceResultInterface;
use MakinaCorpus\Calista\Query\Query;

final class FormDataDatasource extends AbstractDatasource
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getItemClass(): string
    {
        return FormData::class;
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getSorts(): array
    {
        return [
            'created_at' => "Date de création",
            'sent_at' => "Date d'envoi",
        ];
    }

    public function supportsStreaming(): bool
    {
        return true;
    }

    public function supportsPagination(): bool
    {
        return true;
    }

    public function getItems(Query $query): DatasourceResultInterface
    {
        $entityQuery = $this->entityManager->query(FormData::class, 'd');

        // Apply filters here.

        $select = $entityQuery->getQuery();

        $total = $select->getCountQuery('count')->execute()->fetchField();
        $select->range($query->getLimit(), $query->getOffset());
        $sortOrder = $query->getSortOrder() === Query::SORT_DESC ? DbQuery::ORDER_DESC : DbQuery::ORDER_ASC;

        switch ($query->getSortField()) {
            case 'created_at': // Default.
                break;
            case 'sent_at':
                $select->orderBy('d.sent_at', $sortOrder);
                break;
        }
        $select->orderBy('d.created_at', $sortOrder);

        return $this->createResult($entityQuery->execute(), $total);
    }
}
