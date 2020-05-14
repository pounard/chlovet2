<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractAppMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20200510171749 extends AbstractAppMigration
{
    public function getDescription(): string
    {
        return 'Gestion des accès uniques et premier formulaire.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
            CREATE TABLE IF NOT EXISTS "client_login" (
                "token" varchar(512) NOT NULL,
                "email" varchar(512) NOT NULL,
                "type" varchar(512) NOT NULL,
                "created_at" timestamp DEFAULT current_timestamp,
                "valid_until" timestamp DEFAULT current_timestamp + interval '7 day',
                "login_count" bigint DEFAULT 0,
                "login_first" timestamp DEFAULT NULL,
                "login_last" timestamp DEFAULT NULL,
                PRIMARY KEY ("token")
            );
            SQL
        );
    }
}
