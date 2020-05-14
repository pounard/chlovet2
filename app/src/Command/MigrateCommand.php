<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\MenuRepository;
use App\Repository\PageRepository;
use Goat\Converter\ConverterInterface;
use Goat\Driver\DriverFactory;
use Goat\Query\Query;
use Goat\Runner\Runner;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Exemple:
 *   bin/console app:migrate pgsql://chlovet:chlovet@localhost/chlovet
 */
final class MigrateCommand extends Command
{
    const SQLDATE = 'Y-m-d H:i:s';

    private ConverterInterface $converter;
    private MenuRepository $menuRepository;
    private PageRepository $pageRepository;
    private Runner $runner;
    protected static $defaultName = 'app:migrate';

    /**
     * Default constructor
     */
    public function __construct(
        Runner $runner,
        ConverterInterface $converter,
        PageRepository $pageRepository,
        MenuRepository $menuRepository
    ) {
        parent::__construct();

        $this->converter = $converter;
        $this->menuRepository = $menuRepository;
        $this->pageRepository = $pageRepository;
        $this->runner = $runner;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Migrate from Drupal 7')
            ->addArgument('database', InputArgument::REQUIRED, 'Database to migrate from DSN')
        ;
    }

    private function createConnection(InputInterface $input): Runner
    {
        return DriverFactory::fromDoctrineConnection(
            \Doctrine\DBAL\DriverManager::getConnection(
                ['url' => $input->getArgument('database')],
                new \Doctrine\DBAL\Configuration()
            ),
            // @todo It assumes we are working with the same DBMS server
            $this->converter
        );
    }

    private function reduceDrupalFieldItem(string $name, array $data): array
    {
        unset(
            $data['entity_type'],
            $data['entity_id'],
            $data['bundle'],
            $data['deleted'],
            $data['revision_id'],
            $data['language'],
            $data['delta']
        );

        $ret = [];
        foreach ($data as $key => $value) {
            if (null === $value || '' === $value) {
                continue;
            }
            if (0 === \strpos($key, $name)) {
                $key = \substr($key, \strlen($name) + 1);
            }
            $ret[$key] = $value;
        }

        return $ret;
    }

    private function key(string $key, ?array $values)
    {
        return $values ? ($values[$key] ?? null) : null;
    }

    private function first(array $values)
    {
        foreach ($values as $value) {
            return $value;
        }
        return null;
    }

    private function getDrupalFieldItems(
        Runner $distRunner, string $entityType, int $entityId,
        int $revisionId, string $name, string $lang = 'und'
    ): ?array
    {
        return \array_map(
            function (array $data) use ($name): array {
                return $this->reduceDrupalFieldItem($name, $data);
            },
            \iterator_to_array(
                $distRunner
                    ->getQueryBuilder()
                    ->select('field_data_'.$name, 'f')
                    ->columnExpression('f.*')
                    ->condition('entity_type', $entityType)
                    ->condition('entity_id', $entityId)
                    ->condition('revision_id', $revisionId)
                    ->condition('language', $lang)
                    ->condition('deleted', 0)
                    ->orderBy('delta', Query::ORDER_ASC)
                    ->execute()
            )
        );
    }

    /*
    private function importImage()
    {
        
    }
     */

    /**
     * Find or create local page
     */
    private function findOrCreateLocalPageWithDistId(string $type, string $distId): UuidInterface
    {
        return $this
            ->runner
            ->getQueryBuilder()
            ->select('drupal_map')
            ->column('local_id')
            ->condition('source_type', $type)
            ->condition('source_id', $distId)
            ->execute()
            ->fetchField() ?? $this->pageRepository->create()->getId()
        ;
    }

    private function importNews(Runner $distRunner, array $row)
    {
        $distId = $row['nid'];
        $distRev = $row['vid'] ?? $row['nid'];
        $localId = $this->findOrCreateLocalPageWithDistId('node', (string)$distId);

        $this->pageRepository->append($localId, $row['title'], [
            'body' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'news_body')
            ),
            'image' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'news_image')
            ),
            'teaser' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'news_teaser')
            ),
            'biblio' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'biblio')
            ),
            'author' =>  $this->key(
                'value',
                $this->first(
                    $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'content_author')
                )
            ),
            'date' => $this->key(
                'date',
                $this->first(
                    $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'content_date')
                )
            ),
        ]);
    }

    private function importPage(Runner $distRunner, array $row)
    {
        $distId = $row['nid'];
        $distRev = $row['vid'] ?? $row['nid'];
        $localId = $this->findOrCreateLocalPageWithDistId('node', (string)$distId);

        $this->pageRepository->append($localId, $row['title'], [
            'body' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'page_body')
            ),
            'image' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'page_image')
            ),
            'teaser' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'page_teaser')
            ),
            'biblio' => $this->first(
                $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'biblio')
            ),
            'author' =>  $this->key(
                'value',
                $this->first(
                    $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'content_author')
                )
            ),
            'date' => $this->key(
                'date',
                $this->first(
                    $this->getDrupalFieldItems($distRunner, 'node', $distId, $distRev, 'content_date')
                )
            ),
        ]);
    }

    /**
     * Recursively display menu tree
     */
    private function displayMenuTree(OutputInterface $output, array $items, $prefix = ''): void
    {
        /** @var \App\Command\MigrationDistTreeItem $item */
        foreach ($items as $item) {
            $output->writeln(\sprintf("%s - %d: %s [%s] %s", $prefix, $item->id, $item->title, $item->localId, $item->route));
            if ($item->children) {
                $this->displayMenuTree($output, $item->children, $prefix.'  ');
            }
        }
    }

    /**
     * Recursively create page route for tree
     */
    private function createMenuItem(OutputInterface $output, array $items, int $parentId = null): void
    {
        /** @var \App\Command\MigrationDistTreeItem $item */
        foreach ($items as $item) {
            $routeId = null;
            if ($parentId) {
                $routeId = $this->menuRepository->insertAsChild($parentId, $item->localId, $item->slug, $item->title);
            } else {
                $routeId = $this->menuRepository->insert($item->localId, $item->slug, $item->title);
            }

            if ($item->children) {
                $this->createMenuItem($output, $item->children, $routeId);
            }
        }
    }

    /**
     * @return MigrationDistTreeItem[]
     */
    private function buildMenuTree(OutputInterface $output, iterable $result): array
    {
        $ret = [];
        $allItems = [];

        foreach ($result as $row) {
            $slug = \URLify::filter($row['title']);
            $item = new MigrationDistTreeItem();
            $item->id = $row['id'];
            $item->localId = $this->findOrCreateLocalPageWithDistId('node', (string)$row['node_id']);
            $item->node_id = $row['node_id'];
            $item->parent_id = $row['parent_id'];
            $item->route = empty($row['parent_id']) ? $slug : null;
            $item->slug = $slug;
            $item->title = $row['title'];
            $item->weight = $row['weight'];
            $allItems[$item->id] = $item;
        }

        foreach ($allItems as $item) {
            if ($item->parent_id) {
                if (isset($allItems[$item->parent_id])) {
                    $allItems[$item->parent_id]->children[] = $item;
                    $item->route = sprintf("%s/%s", $allItems[$item->parent_id]->route, $item->slug);
                } else {
                    $output->writeln("<error>".\sprintf("Menu item %d (%s) has a non existing parent %d", $item->id, $item->title, $item->parent_id)."</error>");
                    $ret[] = $item;
                }
            } else {
                $ret[] = $item;
            }
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $distRunner = $this->createConnection($input);
        $distBuilder = $distRunner->getQueryBuilder();

        /*
        $output->writeln("<info>Pages migration</info>");
        $result = $distBuilder
            ->select('node', 'n')
            ->column('*')
            ->leftJoin('node_revision', 'r.vid = n.vid', 'r')
            ->condition('n.type', ['page', 'news'])
            ->orderBy('n.nid', Query::ORDER_ASC)
            ->execute()
        ;

        $progress = new ProgressBar($output, $result->countRows());
        foreach ($result as $row) {
            if ('page' === $row['type']) {
                $this->importPage($distRunner, $row);
            } else {
                $this->importNews($distRunner, $row);
            }
            $progress->advance();
        }
        $progress->finish();
        $output->writeln("");
         */

        $output->writeln("<info>Menu migration</info>");
        $menuId = $distBuilder
            ->select('umenu')
            ->column('id')
            ->condition('is_main', 1)
            ->execute()
            ->fetchField()
        ;

        if ($menuId) {

            $result = $distBuilder
                ->select('umenu_item', 'm')
                ->column('m.*')
                ->columnExpression('coalesce(m.title, node.title)', 'title')
                ->leftJoin('node', 'node.nid = m.node_id')
                ->condition('m.menu_id', $menuId)
                ->orderBy('m.parent_id', Query::ORDER_ASC, Query::NULL_FIRST)
                ->orderBy('m.weight', Query::ORDER_ASC)
                ->orderBy('m.id', Query::ORDER_ASC, Query::NULL_FIRST)
                ->execute()
            ;

            $tree = $this->buildMenuTree($output, $result);
            if ($output->isVeryVerbose()) {
                $this->displayMenuTree($output, $tree);
            }
            $this->createMenuItem($output, $tree);

        } else {
            $output->writeln("Skipping menu migration, no main menu found.");
        }
    }
}

final class MigrationDistTreeItem
{
    public $children = [];
    public $id;
    public $localId;
    public $node_id;
    public $parent_id;
    public $route;
    public $slug;
    public $title;
    public $weight;
}
