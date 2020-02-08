<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class SecurityController extends AbstractController
{
    public function login(Request $request): Response
    {
        if ($this->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            return $this->redirectToRoute('admin_index');
        }

        $isError = false;

        if ($request->hasSession()) {
            $session = $request->getSession();

            $exception = $session->get(Security::AUTHENTICATION_ERROR);
            \assert($exception instanceof AuthenticationException);

            if ($exception) {
                $isError = true;
                $session->remove(Security::AUTHENTICATION_ERROR);
            }
        }

        return $this->render('gestion/security/login.html.twig', [
            'is_error' => $isError,
        ]);
    }
}
