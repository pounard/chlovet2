<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\PageRouteRepository;

final class PageRouteAdminController
{
    use ControllerTrait;

    const ITEM_ROOT = 0;
    const RELATIVE_CHILD = 'child';
    const RELATIVE_AFTER = 'after';
    const RELATIVE_BEFORE = 'before';

    public function tree(Request $request): Response
    {
        throw new \Exception("Not implemented yet");
    }

    private function ensureItem(PageRouteRepository $repository, string $id): int
    {
        return $repository->exists((int)$id) || $this->unfound();
    }

    public function create(PageRouteRepository $repository, Request $request, string $pageId, string $relativeTo = self::ITEM_ROOT, string $position = self::RELATIVE_CHILD): Response
    {
        $id = $this->uuid($pageId);

        if (empty($relativeTo)) {
            switch ($position) {

                case self::RELATIVE_CHILD:
                    $repository->insert($id);
                    break;

                default:
                    $this->unfound();
            }
        } else {
            $otherItemId = $this->ensureItem($repository, $relativeTo);

            switch ($position) {

                case self::RELATIVE_CHILD:
                    $repository->insertAsChild($otherItemId, $id);
                    break;

                case self::RELATIVE_AFTER:
                    $repository->insertAfter($otherItemId, $id);
                    break;

                case self::RELATIVE_BEFORE:
                    $repository->insertBefore($otherItemId, $id);
                    break;

                default:
                    $this->unfound();
            }
        }
    }

    public function move(PageRouteRepository $repository, Request $request, string $id, string $relativeTo = self::ITEM_ROOT, string $position = self::RELATIVE_CHILD)
    {
        $itemId = $this->ensureItem($repository, $id);

        if (empty($relativeTo)) {
            switch ($position) {

                case self::RELATIVE_CHILD:
                    $repository->moveToRoot($itemId);
                    break;

                default:
                    $this->unfound();
            }
        } else {
            $otherItemId = $this->ensureItem($repository, $relativeTo);

            switch ($position) {

                case self::RELATIVE_CHILD:
                    $repository->moveAsChild($otherItemId, $itemId);
                    break;

                case self::RELATIVE_AFTER:
                    $repository->moveAfter($otherItemId, $itemId);
                    break;

                case self::RELATIVE_BEFORE:
                    $repository->moveBefore($otherItemId, $itemId);
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
