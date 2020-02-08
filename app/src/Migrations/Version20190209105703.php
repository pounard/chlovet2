<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\AbstractAppMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Database initialisation
 */
final class Version20190209105703 extends AbstractAppMigration
{
    public function getDescription(): string
    {
        return "Database initialisation, you need the 'uuid-ossp' pgsql extension to be enabled in your database.";
    }

    public function up(Schema $schema): void
    {
        // If no corresponding line exists in "page_state" it means that
        // page is unpublished and will not be visible in front.
        $this->addSql(<<<SQL
create table if not exists "page" (
    "id" uuid not null default uuid_generate_v4(),
    "created_at" timestamp not null default now(),
    "current_revision" integer default null,
    primary key("id")
);
SQL
        );

        $this->addSql(<<<SQL
create table if not exists "page_revision" (
    "id" uuid not null,
    "revision" integer not null,
    "created_at" timestamp not null default now(),
    "title" varchar(128) not null,
    "data" jsonb default null,
    primary key ("id", "revision"),
    foreign key ("id")
        references "page" ("id")
        on delete cascade
);
SQL
        );

    $this->addSql(<<<SQL
create table if not exists "page_route" (
    "id" bigserial not null,
    "page_id" uuid default null,
    "slug" varchar(255) not null,
    "title" varchar(255) default null,
    "route" varchar(1024) not null,
    "weight" int default 0,
    "parent_id" bigint default null,
    primary key("id"),
    unique ("parent_id", "slug"),
    foreign key ("page_id")
        references "page" ("id")
        on delete set null,
    foreign key ("parent_id")
        references "page_route" ("id")
);
SQL
        );
    }
}
