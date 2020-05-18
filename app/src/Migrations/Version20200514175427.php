<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractAppMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20200514175427 extends AbstractAppMigration
{
    public function getDescription(): string
    {
        return 'Stockage des réponses de formulaire';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
            CREATE TABLE IF NOT EXISTS "client" (
                "id" uuid,
                "email" varchar(512) NOT NULL,
                "contact_nom" VARCHAR(500) DEFAULT NULL,
                "contact_prenom" VARCHAR(500) DEFAULT NULL,
                "created_at" timestamp NOT NULL DEFAULT current_timestamp,
                PRIMARY KEY ("id"),
                UNIQUE ("email")
            );
            SQL
        );

        $this->addSql(
            <<<SQL
            CREATE TABLE IF NOT EXISTS "form_data" (
                "id" uuid NOT NULL,
                "client_id" uuid DEFAULT NULL,
                "type" varchar(512) NOT NULL DEFAULT 'default',
                "created_at" timestamp DEFAULT current_timestamp,
                "sent_at" timestamp DEFAULT NULL,
                "data" jsonb NOT NULL,
                "data_as_text" TEXT DEFAULT NULL,
                PRIMARY KEY ("id"),
                FOREIGN KEY ("client_id")
                    REFERENCES "client" ("id")
                    ON DELETE CASCADE
            );
            SQL
        );
    }
}
