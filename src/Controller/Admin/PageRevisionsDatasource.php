<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\PageRevision;
use App\Repository\PageRepository;
use Goat\Query\Query as DbQuery;
use MakinaCorpus\Calista\Datasource\AbstractDatasource;
use MakinaCorpus\Calista\Datasource\DatasourceResultInterface;
use MakinaCorpus\Calista\Query\Filter;
use MakinaCorpus\Calista\Query\Query;

final class PageRevisionsDatasource extends AbstractDatasource
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
        return [
            (new Filter('id'))
                ->setArbitraryInput(),
        ];
    }

    public function getSorts(): array
    {
        return [
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
            ->leftJoin('page_revision', 'p.id = pr.id', 'pr')
        ;

        if ($query->has('id')) {
            $select->condition('p.id', $query->get('id'));
        }
        /*
        if ($query->has('gestion')) {
            $criteria['demande.gestionnaire_id'] = $query->get('gestion');
        }
         */

        $total = $select->getCountQuery('count')->execute()->fetchField();
        $select->range($query->getLimit(), $query->getOffset());
        $sortOrder = $query->getSortOrder() === Query::SORT_DESC ? DbQuery::ORDER_DESC : DbQuery::ORDER_ASC;

        switch ($query->getSortField()) {
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
