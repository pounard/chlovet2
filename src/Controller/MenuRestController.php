<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\MenuItem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    public function tree(): Response
    {
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
