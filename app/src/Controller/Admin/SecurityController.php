<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class SecurityController extends AbstractController
{
    private ?string $securityAuthTokenDangerZone = null;

    public function __construct(?string $securityAuthTokenDangerZone = null)
    {
        $this->securityAuthTokenDangerZone = $securityAuthTokenDangerZone;
    }

    public function login(Request $request): Response
    {
        if ($this->isGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)) {
            return $this->redirectToRoute('admin_index');
        }

        $isError = false;

        if ($request->hasSession()) {
            $session = $request->getSession();

            $exception = $session->get(Security::AUTHENTICATION_ERROR);

            if ($exception) {
                \assert($exception instanceof AuthenticationException);

                $isError = true;
                $session->remove(Security::AUTHENTICATION_ERROR);
            }
        }

        return $this->render('gestion/security/login.html.twig', [
            'is_error' => $isError,
        ]);
    }

    public function phpinfo(Request $request): Response
    {
        if (!$this->securityAuthTokenDangerZone || $this->securityAuthTokenDangerZone !== $request->get('token')) {
            throw $this->createNotFoundException();
        }

        return new StreamedResponse(fn () => \phpinfo());
    }
}
