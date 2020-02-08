<?php

declare(strict_types=1);

namespace App\Controller\Endpoint;

use App\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageRestController
{
    use ControllerTrait;

    public function info(Request $request, string $id): Response
    {
        return $this->serialize(
            $request,
            $this->req(
                $this->repository->info(
                    $this->uuid($id)
                )
            )
        );
    }

    public function current(Request $request, string $id): Response
    {
        return $this->serialize(
            $request,
            $this->req(
                $this->repository->current(
                    $this->uuid($id)
                )
            )
        );
    }

    public function create(Request $request): Response
    {
        return $this->serialize($request, $this->repository->create());
    }

    /**
     * Query body must be key/value pairs
     */
    public function find(): Response
    {
        throw new \Exception("Not implemented yet");
    }

    public function revisions(string $id): Response
    {
        throw new \Exception("Not implemented yet");
    }

    public function append(Request $request, string $id): Response
    {
        return $this->serialize(
            $request,
            $this->req(
                $this->repository->append(
                    $this->uuid($id),
                    $this->req(
                        $request->get('title')
                    ),
                    $this->decjson(
                        $this->req(
                            $request->get('data')
                        )
                    )
                )
            )
        );
    }

    public function setCurrent(string $id, int $revision): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }

    public function delete(string $id, int $revision): Response
    {
        if (!$page = $this->repository->info($this->uuid($id))) {
            throw new NotFoundHttpException();
        }

        throw new \Exception("Not implemented yet");
    }
}
