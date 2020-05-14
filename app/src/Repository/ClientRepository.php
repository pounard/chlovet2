<?php

declare(strict_types=1);

namespace App\Repository;

use Goat\Mapper\Tests\Mock\Client;

interface ClientRepository
{
    /**
     * Update client, insert if it does not exists.
     */
    public function upsert(string $email, ?array $values = null): Client;
}
