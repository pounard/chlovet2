<?php

namespace App\Hydrator\Entity;

class FormDataHydrator implements \Zend\Hydrator\HydratorInterface
{
    private $hydrateCallbacks = array(), $extractCallbacks = array();
    function __construct()
    {
        $this->hydrateCallbacks[] = \Closure::bind(static function ($object, $values) {
            if (isset($values['id']) || $object->id !== null && \array_key_exists('id', $values)) {
                $object->id = $values['id'];
            }
            if (isset($values['clientId']) || $object->clientId !== null && \array_key_exists('clientId', $values)) {
                $object->clientId = $values['clientId'];
            }
            if (isset($values['type']) || $object->type !== null && \array_key_exists('type', $values)) {
                $object->type = $values['type'];
            }
            if (isset($values['data']) || $object->data !== null && \array_key_exists('data', $values)) {
                $object->data = $values['data'];
            }
            if (isset($values['createdAt']) || $object->createdAt !== null && \array_key_exists('createdAt', $values)) {
                $object->createdAt = $values['createdAt'];
            }
            $object->sentAt = $values['sentAt'] ?? null;
        }, null, 'App\\Entity\\FormData');
        $this->extractCallbacks[] = \Closure::bind(static function ($object, &$values) {
            $values['id'] = $object->id;
            $values['clientId'] = $object->clientId;
            $values['type'] = $object->type;
            $values['data'] = $object->data;
            $values['createdAt'] = $object->createdAt;
            $values['sentAt'] = $object->sentAt;
        }, null, 'App\\Entity\\FormData');
    }
    function hydrate(array $data, $object)
    {
        $this->hydrateCallbacks[0]->__invoke($object, $data);
        return $object;
    }
    function extract($object)
    {
        $ret = array();
        $this->extractCallbacks[0]->__invoke($object, $ret);
        return $ret;
    }
}