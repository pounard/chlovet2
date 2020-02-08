<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class PageController extends AbstractController
{
    use ControllerTrait;

    public function home(): Response
    {
        return $this->render('home.html.twig');
    }

    public function page(string $id): Response
    {
        return $this->render('page.html.twig', [
            'page' => $this->req(
                $this->repository->current(
                    $this->uuid($id)
                )
            ),
        ]);
    }
}
