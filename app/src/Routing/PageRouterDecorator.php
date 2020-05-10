<?php

declare(strict_types=1);

namespace App\Routing;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;

final class PageRouterDecorator implements RouterInterface, RequestMatcherInterface
{
    private RouterInterface $nested;

    public function __construct(RouterInterface $router)
    {
        $this->nested = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function setContext(RequestContext $context) /* : void */
    {
        $this->nested->setContext($context);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext() /* : RequestContext */
    {
        return $this->nested->getContext();
    }

    /**
     * Tries to match a request with a set of routes.
     *
     * If the matcher can not find information, it must throw one of the exceptions documented
     * below.
     *
     * @return array An array of parameters
     *
     * @throws NoConfigurationException  If no routing configuration could be found
     * @throws ResourceNotFoundException If no matching resource could be found
     * @throws MethodNotAllowedException If a matching resource was found but the request method is not allowed
     */
    public function matchRequest(Request $request) /* : bool */
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection() /* : RouteCollection */
    {
        return $this->nested->getRouteCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo) /* : array */
    {
        throw new \Exception("Implement me");
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH) /* :string */
    {
        throw new \Exception("Implement me");
    }

    /**
     * {@inheritdoc}
     */
    public function matches(Request $request) /* : bool */
    {
        throw new \Exception("Implement me");
    }
}
