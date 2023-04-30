<?php

namespace App\Controller;

use App\Entity\Color;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\ColrAPIService;

class CreateColorController extends AbstractController
{
    public function __construct(
        private ColrAPIService $colrAPIService
    ) {
    }

    #[Route('/api/create/color', name: 'create_color', methods: ['POST'])]
    public function createColor(Request $request): Response
    {
        try {
            $bodyData = $request->toArray();
            $this->colrAPIService->handleColor();

            return new Response('Color created successfully!');
        } catch (Exception $e) {
            echo 'The error is: ',  $e->getMessage(), "\n";
        }
    }
}
