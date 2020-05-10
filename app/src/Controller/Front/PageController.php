<?php

declare(strict_types=1);

namespace App\Controller\Front;

use App\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class PageController extends AbstractController
{
    use ControllerTrait;

    private bool $isFrontEnabled = true;

    public function __construct(bool $isFrontEnabled)
    {
        $this->isFrontEnabled = $isFrontEnabled;
    }

    public function home(): Response
    {
        if (!$this->isFrontEnabled) {
            return $this->render('page/off.html.twig');
        }

        return $this->render('page/home.html.twig');
    }

    public function page(string $id): Response
    {
        if (!$this->isFrontEnabled) {
            return $this->redirectToRoute('index');
        }

        return $this->render('page/page.html.twig', [
            'page' => $this->req(
                $this->repository->current(
                    $this->uuid($id)
                )
            ),
        ]);
    }
}
