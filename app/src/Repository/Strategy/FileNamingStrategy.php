<?php

declare(strict_types=1);

namespace App\Repository\Strategy;

use App\Entity\EditorialFile;

class FileNamingStrategy
{
    public function createTargetFilename(EditorialFile $file): string
    {
        $createdAt = $file->getCreatedAt();

        return \sprintf(
            "%s/%s/%s/%s",
            $createdAt->format('Y'),
            $createdAt->format('m'),
            $createdAt->format('d'),
            $file->getName()
        );
    }
}
