<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse() ?? new RedirectResponse('/');

        // Créer un cookie expiré pour écraser l'ancien
        $cookie = Cookie::create('user_uuid')
            ->withValue('')
            ->withExpires(time() - 3600)
            ->withSecure(false)
            ->withHttpOnly(true)
            ->withPath('/');

        $response->headers->setCookie($cookie);
        $event->setResponse($response);
    }
} 