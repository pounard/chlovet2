<?php

declare(strict_types=1);

namespace DoctrineMigrations;

/*
use App\Migrations\AbstractAppMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20200202180618 extends AbstractAppMigration
{
    public function getDescription() : string
    {
        return 'Creates schema for additional content disposition';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql(<<<SQL
create table "editorial_file" (
    "id" uuid not null,
    "created_at" timestamp not null default current_timestamp,
    "name" varchar(255) not null,
    "uri" varchar(1024) not null,
    "mimetype" varchar(64) default null,
    "filesize" int default null,
    "sha1sum" varchar(64) default null,
    primary key ("id")
);
SQL
        );
    }
}
 */