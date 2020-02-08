<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EditorialFile;
use App\Repository\Strategy\FileNamingStrategy;
use Goat\Runner\Runner;
use MakinaCorpus\FilechunkBundle\FileManager;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @deprecated
 *   Soon to be replaced by a more mature interface and set of tools.
 */
final class EditorialFileRepository
{
    private $runner;
    private $fileManager;
    private $namingStrategy;
    private $storageDirectory;

    /**
     * Default constructor
     */
    public function __construct(Runner $runner, FileManager $fileManager, string $storageDirectory)
    {
        $this->fileManager = $fileManager;
        $this->namingStrategy = new FileNamingStrategy();
        $this->runner = $runner;
        $this->storageDirectory = $storageDirectory;
    }

    /**
     * {@internal}
     */
    public function getRunner(): Runner
    {
        return $this->runner;
    }

    /**
     * Get type map for iterator results (performance improvement)
     */
    private function getTypeMap(): array
    {
        return [
            'created_at' => 'timestamp',
            'filesize' => 'int',
            'id' => 'uuid',
            'mimetype' => 'varchar',
            'name' => 'varchar',
            'sha1sum' => 'varchar',
            'uri' => 'varchar',
        ];
    }

    /**
     * Create new page
     */
    public function create(string $uri, bool $doMove = false): EditorialFile
    {
        $file = EditorialFile::fromFile($uri);

        // @todo Search for a potential duplicate

        if ($doMove) {
            $target = $this->storageDirectory.'/'.$this->namingStrategy->createTargetFilename($file);
            $uri = $this->fileManager->rename($uri, \dirname($target), FileManager::MOVE_CONFLICT_RENAME);
        } else {
            $uri = $this->fileManager->getURI($uri);
        }

        return $this
            ->runner
            ->getQueryBuilder()
            ->insertValues('editorial_file')
            ->values([
                'created_at' => $file->getCreatedAt(),
                'uri' => $uri,
                'filesize' => $file->getFilesize(),
                'id' => Uuid::uuid4(),
                'mimetype' => $file->getMimetype(),
                'name' => $file->getName(),
                'sha1sum' => $file->getSha1Summary(),
            ])
            ->returning('*')
            ->setOption('class', EditorialFile::class)
            ->execute()
            ->fetch()
        ;
    }

    /**
     * Delete a complete page, along all revisions
     */
    public function delete(UuidInterface $id): int
    {
        $queryBuilder = $this->runner->getQueryBuilder();

        $queryBuilder
            ->delete('public.page_revision')
            ->condition('id', $id)
            ->execute()
        ;

        return $queryBuilder
            ->delete('public.page')
            ->condition('id', $id)
            ->execute()
            ->countRows()
        ;
    }
}
