<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FormData;
use Goat\Mapper\Error\EntityDoesNotExistError;
use Ramsey\Uuid\UuidInterface;

interface FormDataRepository
{
    /**
     * Insert form data.
     */
    public function insert(string $type, array $data, ?UuidInterface $clientId = null): FormData;

    /**
     * Find single entry.
     *
     * @throws EntityDoesNotExistError
     *   If entity was not found.
     */
    public function findOne(UuidInterface $id): FormData;
}
