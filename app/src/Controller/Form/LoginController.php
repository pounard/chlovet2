<?php

declare(strict_types=1);

namespace App\Controller\Form;

use App\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class LoginController extends AbstractController
{
    use ControllerTrait;

    private bool $isFrontEnabled = true;

    public function __construct(bool $isFrontEnabled)
    {
        $this->isFrontEnabled = $isFrontEnabled;
    }

    public function login(Request $request): Response
    {
        if ($form = $request->get('form')) {
            switch ($form) {

                case 'commemoratif':
                    return $this->redirectToRoute('form_commemoratif');
            }
        }

        return $this->redirectToRoute('form_home');
    }

    public function expire(): Response
    {
        return $this->render('form/security/expire.html.twig');
    }
}
