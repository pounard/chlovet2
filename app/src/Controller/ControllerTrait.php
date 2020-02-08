<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\PageRepository;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;

trait ControllerTrait
{
    private bool $debug = false;
    private MenuRepository $menuRepository;
    private PageRepository $repository;
    private SerializerInterface $serializer;

    public function __construct(PageRepository $repository, MenuRepository $menuRepository, SerializerInterface $serializer, bool $debug = false)
    {
        $this->debug = $debug;
        $this->menuRepository = $menuRepository;
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    private function isDebug(): bool
    {
        return $this->debug;
    }

    private function serialize(Request $request, $data): Response
    {
        // @todo Content type negociation
        $format = 'json';
        $contentType = 'application/json';

        $output = $this->serializer->serialize($data, $format);

        return new Response($output, Response::HTTP_OK, ['Content-type' => $contentType]);
    }

    private function uuid(string $value): UuidInterface
    {
        try {
            return Uuid::fromString($value);
        } catch (\Exception $e) {
            throw new NotFoundHttpException(null, $e);
        }
    }

    private function iterable($value): iterable
    {
        if (!\is_iterable($value)) {
            throw new BadRequestHttpException();
        }
        return $value;
    }

    private function unfound(): void
    {
        throw new NotFoundHttpException();
    }

    private function invalid(): void
    {
        throw new BadRequestHttpException();
    }

    private function req($value)
    {
        return $value ?? $this->unfound();
    }

    private function decjson($value): string
    {
        return ($ret = \json_decode($value, true)) ? $ret : $this->invalid();
    }
}
