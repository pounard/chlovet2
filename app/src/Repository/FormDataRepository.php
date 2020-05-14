<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\FormData;

interface FormDataRepository
{
    /**
     * Insert form data.
     */
    public function insert(string $type, array $data): FormData;
}
