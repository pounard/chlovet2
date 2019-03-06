<?php

declare(strict_types=1);

namespace App\Entity;

final class Menu
{
    use MenuTrait;

    /**
     * In order for this to work, and this is a hard pre-requisite, even if
     * children are not ordered right, every parent must happen to be before
     * all of its children in the iterable.
     *
     * This is due to how PostgreSQL recursive CTE query restitues the result,
     * sadly because order by is not implemented in recrusive CTE queries, we
     * have to manually order children, it will be lazily done on the first
     * getChildren() call on the MenuTrait trait.
     *
     * You've been warned, orphans will just be put at the bottom of the list.
     */
    public function __construct(iterable $items, ?int $rootId = null, bool $debug = false)
    {
        $parentMap = [];

        $addChild = \Closure::bind(
            function (MenuItem $item, MenuItem$child) {
                $item->children[] = $child;
            },
            null,
            MenuItem::class
        );

        /** @var \App\Entity\MenuItem $item */
        foreach ($items as $item) {
            $parentMap[$id = $item->getId()] = $item;
            $parentId = $item->getParentId();

            if (($rootId && $rootId === $id) || !$parentId) {
                $this->children[$id] = $item;
            } else if (isset($parentMap[$parentId])) {
                \call_user_func($addChild, $parentMap[$parentId], $item);
            } else if ($debug) {
                throw new \Exception("There should not be any orphans");
            } else {
                \call_user_func($addChild, $parentMap[$parentId], $item);
            }
        }
    }
}
