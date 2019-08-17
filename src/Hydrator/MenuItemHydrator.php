<?php

declare(strict_types=1);

namespace App\Hydrator;

use App\Entity\MenuItem;

/**
 * This is a generated hydrator for the App\Entity\MenuItem class
 */
final class MenuItemHydrator
{
    private static $useConstructor = false;
    private static $reflection;

    public static $hydrate0;
    public static $extract0;

    /**
     * Hydrate given object instance with given data
     */
    public static function hydrate(array $data, MenuItem $object): MenuItem
    {
        self::$hydrate0->__invoke($object, $data);
        return $object;
    }

    /**
     * Extract object data as a key-value array whose keys are properties names
     */
    public static function extract(MenuItem $object): array
    {
        $ret = [];
        self::$extract0->__invoke($object, $ret);
        return $ret;
    }

    /**
     * Create a new instance and hydate it with given data
     */
    public static function create(array $data): MenuItem
    {
        if (self::$useConstructor) {
            $object = new MenuItem();
        } else {
            $object = (self::$reflection ?? (
                self::$reflection = new \ReflectionClass(MenuItem::class))
            )->newInstanceWithoutConstructor();
        }

        return self::hydrate($data, $object);
    }
}

MenuItemHydrator::$hydrate0 = \Closure::bind(static function ($object, $values) {
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

MenuItemHydrator::$extract0 = \Closure::bind(static function ($object, &$values) {
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