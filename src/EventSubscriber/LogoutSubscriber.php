<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function onLogoutEvent(LogoutEvent $event): void
    {
        // dd($event->getRequest()->getAcceptableContentTypes());
        if (in_array('application/json', $event->getRequest()->getAcceptableContentTypes())) {
            // $event->getResponse(); // On peut récupérer les infos de la réponse, et avec getRequest, on pourrait voir si la requête a été faite en json
            // $event->setResponse(new JsonResponse()); // ici, on récupère une réponse 200, ce qui n'est pas exactement ce qu'on veut
            $event->setResponse(new JsonResponse(null, Response::HTTP_NO_CONTENT)); // ici, on récupère une réponse 204, no content
            // ou $event->setResponse(new JsonResponse(null, 204)); 
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
