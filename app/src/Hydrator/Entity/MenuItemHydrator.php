<?php

namespace App\Hydrator\Entity;

class MenuItemHydrator implements \Zend\Hydrator\HydratorInterface
{
    private $hydrateCallbacks = array(), $extractCallbacks = array();
    function __construct()
    {
        $this->hydrateCallbacks[] = \Closure::bind(static function ($object, $values) {
            if (isset($values['parent_id']) || $object->parent_id !== null && \array_key_exists('parent_id', $values)) {
                $object->parent_id = $values['parent_id'];
            }
            if (isset($values['route']) || $object->route !== null && \array_key_exists('route', $values)) {
                $object->route = $values['route'];
            }
            if (isset($values['slug']) || $object->slug !== null && \array_key_exists('slug', $values)) {
                $object->slug = $values['slug'];
            }
            if (isset($values['title']) || $object->title !== null && \array_key_exists('title', $values)) {
                $object->title = $values['title'];
            }
            if (isset($values['weight']) || $object->weight !== null && \array_key_exists('weight', $values)) {
                $object->weight = $values['weight'];
            }
            if (isset($values['children']) || $object->children !== null && \array_key_exists('children', $values)) {
                $object->children = $values['children'];
            }
            if (isset($values['id']) || $object->id !== null && \array_key_exists('id', $values)) {
                $object->id = $values['id'];
            }
            if (isset($values['page_id']) || $object->page_id !== null && \array_key_exists('page_id', $values)) {
                $object->page_id = $values['page_id'];
            }
            if (isset($values['sorted']) || $object->sorted !== null && \array_key_exists('sorted', $values)) {
                $object->sorted = $values['sorted'];
            }
        }, null, 'App\\Entity\\MenuItem');
        $this->extractCallbacks[] = \Closure::bind(static function ($object, &$values) {
            $values['parent_id'] = $object->parent_id;
            $values['route'] = $object->route;
            $values['slug'] = $object->slug;
            $values['title'] = $object->title;
            $values['weight'] = $object->weight;
            $values['children'] = $object->children;
            $values['id'] = $object->id;
            $values['page_id'] = $object->page_id;
            $values['sorted'] = $object->sorted;
        }, null, 'App\\Entity\\MenuItem');
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