<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Authenticate users with one time login.
 */
final class FormClientAuthenticator extends AbstractGuardAuthenticator
{
    private FormClientTokenRepository $tokenRepository;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(FormClientTokenRepository $tokenRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->tokenRepository = $tokenRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        return 'form_login' === $request->attributes->get('_route');
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        return ['token' => $request->get('token')];
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!isset($credentials['token'])) {
            throw new AuthenticationException();
        }

        return $this->tokenRepository->loadUserByToken($credentials['token']);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!isset($credentials['token'])) {
            throw new AuthenticationException();
        }

        return $this->tokenRepository->touch($credentials['token']);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null; // Let request pass and continue.
    }

    /**
     * Lien vers la page de lien expiré.
     */
    private function createRedirectToExpire(Request $request): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('form_expire'), Response::HTTP_SEE_OTHER);
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->createRedirectToExpire($request);
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->createRedirectToExpire($request);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
