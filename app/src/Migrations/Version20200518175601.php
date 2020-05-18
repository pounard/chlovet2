<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractAppMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20200518175601 extends AbstractAppMigration
{
    public function getDescription(): string
    {
        return 'Corrige le schéma de quelques tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<SQL
            ALTER TABLE "form_data"
                ADD COLUMN IF NOT EXISTS "data_as_text" TEXT DEFAULT NULL
            SQL
        );

        $this->addSql(
            <<<SQL
            ALTER TABLE "client"
                ADD COLUMN IF NOT EXISTS "contact_nom" VARCHAR(500) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS "contact_prenom" VARCHAR(500) DEFAULT NULL,
                ADD COLUMN IF NOT EXISTS "created_at" timestamp NOT NULL DEFAULT current_timestamp
            SQL
        );
    }
}
