<?php

declare(strict_types=1);

namespace App\Generated\Mapper\Definition\Entity;

final class FormData
{
    public static function getDefinition(\Goat\Mapper\Definition\Registry\DefinitionRegistry $registry): \Goat\Mapper\Definition\Graph\Entity
    {
        $ret = new \Goat\Mapper\Definition\Graph\Impl\DefaultEntity(\App\Entity\FormData::class);

        $table = new \Goat\Mapper\Definition\Table('form_data', null);
        $ret->setTable($table);

        $key = new \Goat\Mapper\Definition\PrimaryKey([
        ]);
        $ret->setPrimaryKey($key);

        $ret->addProperty(new \Goat\Mapper\Definition\Graph\Impl\DefaultValue('id', 'id', null));
        $ret->addProperty(new \Goat\Mapper\Definition\Graph\Impl\DefaultValue('clientId', 'client_id', null));
        $ret->addProperty(new \Goat\Mapper\Definition\Graph\Impl\DefaultValue('type', 'type', null));
        $ret->addProperty(new \Goat\Mapper\Definition\Graph\Impl\DefaultValue('createdAt', 'created_at', null));
        $ret->addProperty(new \Goat\Mapper\Definition\Graph\Impl\DefaultValue('sentAt', 'sent_at', null));
        $ret->addProperty(new \Goat\Mapper\Definition\Graph\Impl\DefaultValue('data', 'data', null));

        return $ret;
    }
}

