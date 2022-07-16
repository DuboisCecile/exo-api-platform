<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{

    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login()
    {
        $user = $this->getUser();
        return $this->json([
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout()
    {
        // on ne met aucune logique ici, cette fonction sert juste à se déconnecter
    }
}
