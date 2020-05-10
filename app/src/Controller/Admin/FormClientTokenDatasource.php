<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Security\FormClientUser;
use Goat\Query\Query as DbQuery;
use Goat\Runner\Runner;
use MakinaCorpus\Calista\Datasource\AbstractDatasource;
use MakinaCorpus\Calista\Datasource\DatasourceResultInterface;
use MakinaCorpus\Calista\Query\Query;

final class FormClientTokenDatasource extends AbstractDatasource
{
    private Runner $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function getItemClass(): string
    {
        return FormClientUser::class;
    }

    public function getFilters(): array
    {
        return [];
    }

    public function getSorts(): array
    {
        return [
            'created_at' => "Date de création",
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
            ->runner
            ->getQueryBuilder()
            ->select('client_login', 'l')
            ->column('*')
        ;

        $total = $select->getCountQuery('count')->execute()->fetchField();
        $select->range($query->getLimit(), $query->getOffset());
        $sortOrder = $query->getSortOrder() === Query::SORT_DESC ? DbQuery::ORDER_DESC : DbQuery::ORDER_ASC;

        switch ($query->getSortField()) {
            case 'created_at':
                break;
        }
        $select->orderBy('l.created_at', $sortOrder);

        // Pas de classe, on va utiliser le row en direct.
        return $this->createResult($select->execute(), $total);
    }
}
