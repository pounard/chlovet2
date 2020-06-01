<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Twig\Environment;

/**
 * @codeCoverageIgnore
 */
final class KernelEventSubscriber implements EventSubscriberInterface
{
    private Environment $twig;

    /**
     * Default constructor
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdo}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
            KernelEvents::REQUEST => 'onRequest',
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    /**
     * Do stuff...
     */
    public function onRequest(RequestEvent $event)
    {
        /*
        $context = SecurityContextManager::fromRequest($event->getRequest());

        if ($context->hasMuna()) {
            $munaIdv = $context->getMuna();
            $this->appContext->setClient($this->clientRepository->findFirst(['munaidv' => $munaIdv]));
        }
         */
    }

    /**
     * Analyse application state and return warnings.
     *
    private function analyseApplicationState(): array
    {
        $ret = [];

        if (!$this->preferences->get('foo_downgrade_validate_signature')) {
            $ret[] = "La connexion adhérent n'est pas sécurisée.";
        }

        return $ret;
    }
     */

    /**
     * Do stuff other...
     */
    public function onController(ControllerEvent $event)
    {
        /*
        if ($context->isGestionnaire() && ($messages = $this->analyseApplicationState())) {
            $this->twig->addGlobal('app_state_messages', $messages);
        } else {
            $this->twig->addGlobal('app_state_messages', null);
        }

        $this->twig->addGlobal('current_client', $this->appContext->getClient());
        $this->twig->addGlobal('espaces', $espaces);
        $this->twig->addGlobal('security_context', $context);
        $this->twig->addGlobal('is_client_preview', $context->isAdherentImpersonation());
         */
    }

    /**
     * We need to sync client profile.
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        /*
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        if ($user instanceof AdherentUser) {
            if (($adherent = $user->getAdherent()) instanceof AdherentParticulier) {
                $this->dispatcher->process(new ClientLogin($adherent));
            }
        }
         */
    }
}
