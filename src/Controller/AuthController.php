<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;


final class AuthController extends AbstractController{

    #[Route('api/auth', name: 'api_valide_tocken', methods: ['GET'])]
    public function validateToken(): JsonResponse
    {  
        return $this->json(200);
    }
}
