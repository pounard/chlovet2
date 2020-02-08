<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Repository\EditorialFileRepository;
use MakinaCorpus\FilechunkBundle\FileEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FileEventSubscriber implements EventSubscriberInterface
{
    private EditorialFileRepository $fileRepository;

    /**
     * Default constructor
     */
    public function __construct(EditorialFileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FileEvent::EVENT_UPLOAD_FINISHED => 'onUploadFinished',
        ];
    }

    /**
     * Act upon file upload.
     */
    public function onUploadFinished(FileEvent $event)
    {
        if ($event->hasFieldConfig() && 'ckeditor' === $event->getFieldConfig()->getName()) {
            // Store file into editorial file repository if originates
            // from the WYSIWYG editor.
            $this->fileRepository->create($event->getFileUri());
        }
    }
}
