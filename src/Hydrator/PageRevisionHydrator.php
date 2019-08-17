<?php

declare(strict_types=1);

namespace App\Hydrator;

use App\Entity\PageRevision;

/**
 * This is a generated hydrator for the App\Entity\PageRevision class
 */
final class PageRevisionHydrator
{
    private static $useConstructor = false;
    private static $reflection;

    public static $hydrate0;
    public static $extract0;

    /**
     * Hydrate given object instance with given data
     */
    public static function hydrate(array $data, PageRevision $object): PageRevision
    {
        self::$hydrate0->__invoke($object, $data);
        return $object;
    }

    /**
     * Extract object data as a key-value array whose keys are properties names
     */
    public static function extract(PageRevision $object): array
    {
        $ret = [];
        self::$extract0->__invoke($object, $ret);
        return $ret;
    }

    /**
     * Create a new instance and hydate it with given data
     */
    public static function create(array $data): PageRevision
    {
        if (self::$useConstructor) {
            $object = new PageRevision();
        } else {
            $object = (self::$reflection ?? (
                self::$reflection = new \ReflectionClass(PageRevision::class))
            )->newInstanceWithoutConstructor();
        }

        return self::hydrate($data, $object);
    }
}

PageRevisionHydrator::$hydrate0 = \Closure::bind(static function ($object, $values) {
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

PageRevisionHydrator::$extract0 = \Closure::bind(static function ($object, &$values) {
    $values['created_at'] = $object->created_at;
    $values['data'] = $object->data;
    $values['id'] = $object->id;
    $values['page_at'] = $object->page_at;
    $values['revision'] = $object->revision;
    $values['title'] = $object->title;
}, null, 'App\\Entity\\PageRevision');