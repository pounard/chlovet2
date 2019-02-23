<?php

namespace App\Hydrator;

use Zend\Hydrator\HydratorInterface;

/**
 * This is a generated hydrator for the App\Entity\PageRevision class.
 *
 * Delete this file to force is regeneration upon next hydrator call.
 */
final class PageRevisionHydrator implements HydratorInterface
{
    private $hydrateCallbacks = [];
    private $extractCallbacks = [];

    public function __construct()
    {
        $this->hydrateCallbacks[] = \Closure::bind(function ($object, $values) {
            if (isset($values['created_at']) || $object->created_at !== null && \array_key_exists('created_at', $values)) {
                $object->created_at = $values['created_at'];
            }
            if (isset($values['data']) || $object->data !== null && \array_key_exists('data', $values)) {
                $object->data = $values['data'];
            }
            if (isset($values['id']) || $object->id !== null && \array_key_exists('id', $values)) {
                $object->id = $values['id'];
            }
            if (isset($values['page_at']) || $object->page_at !== null && \array_key_exists('page_at', $values)) {
                $object->page_at = $values['page_at'];
            }
            if (isset($values['revision']) || $object->revision !== null && \array_key_exists('revision', $values)) {
                $object->revision = $values['revision'];
            }
            if (isset($values['title']) || $object->title !== null && \array_key_exists('title', $values)) {
                $object->title = $values['title'];
            }
        }, null, 'App\\Entity\\PageRevision');

        $this->extractCallbacks[] = \Closure::bind(function ($object, &$values) {
            $values['created_at'] = $object->created_at;
            $values['data'] = $object->data;
            $values['id'] = $object->id;
            $values['page_at'] = $object->page_at;
            $values['revision'] = $object->revision;
            $values['title'] = $object->title;
        }, null, 'App\\Entity\\PageRevision');

    }

    public function hydrate(array $data, $object)
    {
        $this->hydrateCallbacks[0]->__invoke($object, $data);
        return $object;
    }

    public function extract($object)
    {
        $ret = array();
        $this->extractCallbacks[0]->__invoke($object, $ret);
        return $ret;
    }
}