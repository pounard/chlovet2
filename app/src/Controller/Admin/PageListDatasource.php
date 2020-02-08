<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\PageRevision;
use App\Repository\PageRepository;
use Goat\Query\Query as DbQuery;
use MakinaCorpus\Calista\Datasource\AbstractDatasource;
use MakinaCorpus\Calista\Datasource\DatasourceResultInterface;
use MakinaCorpus\Calista\Query\Query;

final class PageListDatasource extends AbstractDatasource
{
    private PageRepository $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getItemClass(): string
    {
        return PageRevision::class;
    }

    public function getFilters(): array
    {
        return [];
        /*
        return [
            (new Filter('exercice', "Année d'exercice"))
                ->setArbitraryInput(true),
            (new Filter('munaidv', "MUNAIDV"))
                ->setArbitraryInput(true),
        ];
         */
    }

    public function getSorts(): array
    {
        return [
            'p.created_at' => "Date de création",
            'pr.created_at' => "Date de dernière modification",
            'pr.title' => "Titre de la page",
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
        $select = $this
            ->repository
            ->getRunner()
            ->getQueryBuilder()
            ->select('page', 'p')
            ->columns(['pr.*', 'page_at' => 'p.created_at'])
            ->column('p.id')
            ->leftJoin('page_revision', 'p.id = pr.id and p.current_revision = pr.revision', 'pr')
        ;

        /*
        if ($query->has('munaidv')) {
            $criteria['demande.munaidv'] = $query->get('munaidv');
        }
        if ($query->has('gestion')) {
            $criteria['demande.gestionnaire_id'] = $query->get('gestion');
        }
         */

        $total = $select->getCountQuery('count')->execute()->fetchField();
        $select->range($query->getLimit(), $query->getOffset());
        $sortOrder = $query->getSortOrder() === Query::SORT_DESC ? DbQuery::ORDER_DESC : DbQuery::ORDER_ASC;

        switch ($query->getSortField()) {
            case 'p.created_at':
                break;
            case 'pr.created_at':
                $select->orderBy('pr.created_at', $sortOrder);
                break;
            case 'pr.title':
                $select->orderBy('pr.title', $sortOrder);
                break;
        }
        $select->orderBy('p.created_at', $sortOrder);

        return $this->createResult($select->execute([], $this->getItemClass()), $total);
    }
}
