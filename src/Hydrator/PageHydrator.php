<?php

declare(strict_types=1);

namespace App\Hydrator;

use App\Entity\Page;

/**
 * This is a generated hydrator for the App\Entity\Page class
 */
final class PageHydrator
{
    private static $useConstructor = false;
    private static $reflection;

    public static $hydrate0;
    public static $extract0;

    /**
     * Hydrate given object instance with given data
     */
    public static function hydrate(array $data, Page $object): Page
    {
        self::$hydrate0->__invoke($object, $data);
        return $object;
    }

    /**
     * Extract object data as a key-value array whose keys are properties names
     */
    public static function extract(Page $object): array
    {
        $ret = [];
        self::$extract0->__invoke($object, $ret);
        return $ret;
    }

    /**
     * Create a new instance and hydate it with given data
     */
    public static function create(array $data): Page
    {
        if (self::$useConstructor) {
            $object = new Page();
        } else {
            $object = (self::$reflection ?? (
                self::$reflection = new \ReflectionClass(Page::class))
            )->newInstanceWithoutConstructor();
        }

        return self::hydrate($data, $object);
    }
}

PageHydrator::$hydrate0 = \Closure::bind(static function ($object, $values) {
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

PageHydrator::$extract0 = \Closure::bind(static function ($object, &$values) {
    $values['created_at'] = $object->created_at;
    $values['revision_at'] = $object->revision_at;
    $values['current_revision'] = $object->current_revision;
    $values['id'] = $object->id;
    $values['title'] = $object->title;
}, null, 'App\\Entity\\Page');