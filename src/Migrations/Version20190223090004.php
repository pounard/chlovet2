<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractAppMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Mapping table for migrations
 */
final class Version20190223090004 extends AbstractAppMigration
{
    public function getDescription() : string
    {
        return 'Mapping table for migrations';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
create table if not exists "public"."drupal_map" (
    "source_type" varchar(64) not null,
    "source_id" varchar(64) not null,
    "local_id" uuid not null,
    primary key("source_type", "source_id")
);
SQL
        );
    }
}
