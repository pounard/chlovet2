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
                "token" VARCHAR(512) NOT NULL,
                "email" VARCHAR(512) NOT NULL,
                "target" VARCHAR(512) NOT NULL DEFAULT 'default',
                "created_at" TIMESTAMP DEFAULT current_timestamp,
                "login_count" BIGINT DEFAULT 0,
                "login_first" TIMESTAMP DEFAULT NULL,
                "login_last" TIMESTAMP DEFAULT NULL,
                PRIMARY KEY ("token")
            );
            SQL
        );
    }
}
