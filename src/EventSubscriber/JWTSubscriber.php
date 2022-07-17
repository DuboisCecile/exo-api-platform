<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use App\Entity\User;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        // $event->getUser() récupère l'utilisateur de Symfony
        // $event->getData() récupère les données envoyées dans le payload
        // $event->getHeader() récupère les entêtes

        $data = $event->getData();
        $data['username'] = $event->getUser()->getUserIdentifier();
        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
