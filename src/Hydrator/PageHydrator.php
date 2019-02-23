<?php

namespace App\Hydrator;

use Zend\Hydrator\HydratorInterface;

/**
 * This is a generated hydrator for the App\Entity\Page class.
 *
 * Delete this file to force is regeneration upon next hydrator call.
 */
final class PageHydrator implements HydratorInterface
{
    private $hydrateCallbacks = [];
    private $extractCallbacks = [];

    public function __construct()
    {
        $this->hydrateCallbacks[] = \Closure::bind(function ($object, $values) {
            if (isset($values['created_at']) || $object->created_at !== null && \array_key_exists('created_at', $values)) {
                $object->created_at = $values['created_at'];
            }
            if (isset($values['revision_at']) || $object->revision_at !== null && \array_key_exists('revision_at', $values)) {
                $object->revision_at = $values['revision_at'];
            }
            if (isset($values['current_revision']) || $object->current_revision !== null && \array_key_exists('current_revision', $values)) {
                $object->current_revision = $values['current_revision'];
            }
            if (isset($values['id']) || $object->id !== null && \array_key_exists('id', $values)) {
                $object->id = $values['id'];
            }
            if (isset($values['title']) || $object->title !== null && \array_key_exists('title', $values)) {
                $object->title = $values['title'];
            }
        }, null, 'App\\Entity\\Page');

        $this->extractCallbacks[] = \Closure::bind(function ($object, &$values) {
            $values['created_at'] = $object->created_at;
            $values['revision_at'] = $object->revision_at;
            $values['current_revision'] = $object->current_revision;
            $values['id'] = $object->id;
            $values['title'] = $object->title;
        }, null, 'App\\Entity\\Page');

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