<?php

declare(strict_types=1);

namespace App\Controller\Endpoint;

use App\Controller\ControllerTrait;
use App\Entity\Menu;
use App\Entity\MenuItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MenuRestController
{
    use ControllerTrait;

    const ITEM_ROOT = 0;
    const RELATIVE_CHILD = 'child';
    const RELATIVE_AFTER = 'after';
    const RELATIVE_BEFORE = 'before';

    private function ensureItem(string $id): int
    {
        return $this->menuRepository->exists((int)$id) || $this->unfound();
    }

    private function menuItemToArray(MenuItem $item): array
    {
        return [
            'children' => \array_values(\array_map([$this, 'menuItemToArray'], $item->getChildren())),
            'id' => $item->getId(),
            'originalTitle' => $item->getTitle(),
            'title' => $item->getTitle(),
            // 'expanded' => true,
            // 'subtitle': string|React.ReactNode;
        ];
    }

    private function menuToArray(Menu $menu): array
    {
        return ['tree' => \array_values(\array_map([$this, 'menuItemToArray'], $menu->getChildren()))];
    }

    /**
     * Flatten and validate incomming tree
     */
    private function validateIncommingTree(iterable $tree, ?int $parentId = null): array
    {
        $ret = [];
        $previousId = null;

        foreach ($tree as $item) {

            if (!$currentId = (int)($item['id'] ?? null)) {
                throw new BadRequestHttpException();
            }

            // Generated tree is a set of commands to pass to the item tree
            // storage in the given order: if 'after' is set, use the "move
            // item after" command, else if 'parent' is set, use "insert as
            // child" command, if none, then "move to root" instead.
            // Since that ALL items are being processed, if any is misplaced
            // temporarily during the transaction, it will be replaced
            // correctly when its own siblings will be moved themselves.
            $ret[] = [
                'id' => $currentId,
                'title' => $item['title'] ?? null,
                'parent' => $parentId,
                'after' => $previousId
            ];

            $previousId = $currentId;

            // Flatten children and dispose them after the current item.
            if ($item['children'] ?? null) {
                foreach ($this->validateIncommingTree($this->iterable($item['children']), $currentId) as $child) {
                    $ret[] = $child;
                }
            }
        }

        return $ret;
    }

    /**
     * From incomming request, find and save tree.
     */
    private function saveTreeFromRequest(Request $request)
    {
        $content = $request->getContent();
        $input = null;

        if (!\is_string($content) || (!$input = @\json_decode($content, true)) || !isset($input['tree'])) {
            throw new BadRequestHttpException();
        }

        $deleted = $existing = [];
        $tx = null;
        $flattened = $this->validateIncommingTree($input['tree']);

        try {
            $tx = $this->menuRepository->getRunner()->beginTransaction()->start();

            foreach ($flattened as $item) {
                $existing[] = $id = $item['id'];
                if ($item['after']) {
                    $this->menuRepository->moveAfter($id, $item['after']);
                } else if ($item['parent']) {
                    $this->menuRepository->moveAsChild($id, $item['parent']);
                } else {
                    $this->menuRepository->moveToRoot($id);
                }
                if ($item['title']) {
                    $this->menuRepository->update($id, null, $item['title']);
                }
            }

            if ($existing) {
                /*
                $args = [':list[]' => $existing];
                $deleted = $this->database->query("SELECT id FROM {umenu_item} WHERE menu_id = :id AND id NOT IN (:list[])", $args)->fetchCol();
                $this->database->query("DELETE FROM {umenu_item} WHERE menu_id = :id AND id NOT IN (:list[])", $args);
                 */
            }

            $tx->commit();

        } catch (\Throwable $e) {
            if ($tx) {
                $tx->rollback();
            }
            throw $e;
        }
    }

    public function tree(Request $request): Response
    {
        if ($request->isMethod('post')) {
            $this->saveTreeFromRequest($request);
        }

        return new JsonResponse(
            $this->menuToArray(
                $this->menuRepository->loadTree()
            )
        );
    }

    public function create(Request $request, string $pageId, string $relativeTo = self::ITEM_ROOT, string $position = self::RELATIVE_CHILD): Response
    {
        $id = $this->uuid($pageId);

        if (empty($relativeTo)) {
            switch ($position) {

                case self::RELATIVE_CHILD:
                    $this->menuRepository->insert($id);
                    break;

                default:
                    $this->unfound();
            }
        } else {
            $otherItemId = $this->ensureItem($relativeTo);

            switch ($position) {

                case self::RELATIVE_CHILD:
                    $this->menuRepository->insertAsChild($otherItemId, $id);
                    break;

                case self::RELATIVE_AFTER:
                    $this->menuRepository->insertAfter($otherItemId, $id);
                    break;

                case self::RELATIVE_BEFORE:
                    $this->menuRepository->insertBefore($otherItemId, $id);
                    break;

                default:
                    $this->unfound();
            }
        }
    }

    public function move(Request $request, string $id, string $relativeTo = self::ITEM_ROOT, string $position = self::RELATIVE_CHILD)
    {
        $itemId = $this->ensureItem($id);

        if (empty($relativeTo)) {
            switch ($position) {

                case self::RELATIVE_CHILD:
                    $this->menuRepository->moveToRoot($itemId);
                    break;

                default:
                    $this->unfound();
            }
        } else {
            $otherItemId = $this->ensureItem($relativeTo);

            switch ($position) {

                case self::RELATIVE_CHILD:
                    $this->menuRepository->moveAsChild($otherItemId, $itemId);
                    break;

                case self::RELATIVE_AFTER:
                    $this->menuRepository->moveAfter($otherItemId, $itemId);
                    break;

                case self::RELATIVE_BEFORE:
                    $this->menuRepository->moveBefore($otherItemId, $itemId);
                    break;

                default:
                    $this->unfound();
            }
        }
    }

    public function delete(string $id, int $revision): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }
}
