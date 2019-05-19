<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\User;

class RedirectUserListener
{
    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var RouterInterface */
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->isUserLogged() && $event->isMasterRequest()) {
            $currentRoute = $event->getRequest()->attributes->get('_route');
            if (!$this->isAuthenticatedUserOnAnonymousPage($currentRoute)) {
                $response = new RedirectResponse($this->router->generate('security_login'));
                $event->setResponse($response);
            }
        }
    }

    private function isUserLogged()
    {

        $isLogged = false;

        if ($token = $this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
            $isLogged = $user instanceof User;
        }
        return $isLogged;
    }

    private function isAuthenticatedUserOnAnonymousPage($currentRoute)
    {
        return strpos($currentRoute, 'wdt') !== false || in_array(
            $currentRoute,
            [
                'resource_download',
                'security_login',
                'security_signup',
                'api_resource_form',
                'api_resource_tree',
                'api_resource_search',
                'resource_download',
            ]
        );
    }
}