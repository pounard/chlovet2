<?php

namespace App\Hydrator;

use Zend\Hydrator\HydratorInterface;

/**
 * This is a generated hydrator for the App\Entity\EditorialFile class.
 *
 * Delete this file to force is regeneration upon next hydrator call.
 */
final class EditorialFileHydrator implements HydratorInterface
{
    private $hydrateCallbacks = [];
    private $extractCallbacks = [];

    public function __construct()
    {
        $this->hydrateCallbacks[] = \Closure::bind(function ($object, $values) {
            if (isset($values['created_at']) || $object->created_at !== null && \array_key_exists('created_at', $values)) {
                $object->created_at = $values['created_at'];
            }
            if (isset($values['uri']) || $object->uri !== null && \array_key_exists('uri', $values)) {
                $object->uri = $values['uri'];
            }
            if (isset($values['filesize']) || $object->filesize !== null && \array_key_exists('filesize', $values)) {
                $object->filesize = $values['filesize'];
            }
            if (isset($values['id']) || $object->id !== null && \array_key_exists('id', $values)) {
                $object->id = $values['id'];
            }
            if (isset($values['mimetype']) || $object->mimetype !== null && \array_key_exists('mimetype', $values)) {
                $object->mimetype = $values['mimetype'];
            }
            if (isset($values['name']) || $object->name !== null && \array_key_exists('name', $values)) {
                $object->name = $values['name'];
            }
            if (isset($values['sha1sum']) || $object->sha1sum !== null && \array_key_exists('sha1sum', $values)) {
                $object->sha1sum = $values['sha1sum'];
            }
        }, null, 'App\\Entity\\EditorialFile');

        $this->extractCallbacks[] = \Closure::bind(function ($object, &$values) {
            $values['created_at'] = $object->created_at;
            $values['uri'] = $object->uri;
            $values['filesize'] = $object->filesize;
            $values['id'] = $object->id;
            $values['mimetype'] = $object->mimetype;
            $values['name'] = $object->name;
            $values['sha1sum'] = $object->sha1sum;
        }, null, 'App\\Entity\\EditorialFile');

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