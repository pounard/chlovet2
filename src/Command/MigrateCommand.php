<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\PageRepository;
use App\Repository\PageRouteRepository;
use Goat\Bridge\Symfony\DependencyInjection\RunnerFactory;
use Goat\Converter\ConverterInterface;
use Goat\Query\Query;
use Goat\Runner\Runner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Exemple:
 *   bin/console app:migrate mysql://irpa_portailvac:irpa_portailvac@localhost/irpa_portailvac
 */
final class MigrateCommand extends Command
{
    const SQLDATE = 'Y-m-d H:i:s';

    private $converter;
    private $pageRepository;
    private $pageRouteRepository;
    private $runner;
    protected static $defaultName = 'app:migrate';

    /**
     * Default constructor
     */
    public function __construct(
        Runner $runner,
        ConverterInterface $converter,
        PageRepository $pageRepository,
        PageRouteRepository $pageRouteRepository
    ) {
        parent::__construct();

        $this->converter = $converter;
        $this->pageRepository = $pageRepository;
        $this->pageRouteRepository = $pageRouteRepository;
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
        return RunnerFactory::createFromDoctrineConnection(
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

    private function importNews(Runner $distRunner, array $row)
    {
        $distId = $row['nid'];
        $distRev = $row['vid'] ?? $row['nid'];
        $exists = false;

        $localId = $this->runner->execute(<<<SQL
select local_id
from drupal_map
where
    source_type = 'node'
    and source_id = ?
SQL
        , [(string)$distId])->fetchField();

        if ($localId) {
            $page = $this->pageRepository->info($localId);
            $exists = true;
        } else {
            $page = $this->pageRepository->create();
            $localId = $page->getId();
        }

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
        $exists = false;

        $localId = $this->runner->execute(<<<SQL
select local_id
from drupal_map
where
    source_type = 'node'
    and source_id = ?
SQL
        , [(string)$distId])->fetchField();

        if ($localId) {
            $page = $this->pageRepository->info($localId);
            $exists = true;
        } else {
            $page = $this->pageRepository->create();
            $localId = $page->getId();
        }

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

        if (!$exists) {
            $this
                ->runner
                ->getQueryBuilder()
                ->insertValues('drupal_map')
                ->values([
                    'source_type' => 'node',
                    'source_id' => (string)$distId,
                    'local_id' => $localId,
                ])
                ->execute()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $distRunner = $this->createConnection($input);
        $distBuilder = $distRunner->getQueryBuilder();

        // On y va mode gros porc, on raffinera après.
        $result = $distBuilder
            ->select('node', 'n')
            ->column('*')
            ->leftJoin('node_revision', 'r.vid = n.vid', 'r')
            ->condition('n.type', 'page')
            ->orderBy('n.nid', Query::ORDER_ASC)
            ->execute()
        ;

        $progress = new ProgressBar($output, $result->countRows());
        foreach ($result as $row) {
            $this->importPage($distRunner, $row);
            $progress->advance();
        }
        $progress->finish();
        $output->writeln("");
    }
}
