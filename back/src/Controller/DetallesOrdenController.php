<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DetallesOrdenController extends AbstractController
{
    #[Route('/detalles/orden', name: 'app_detalles_orden')]
    public function index(): Response
    {
        return $this->render('detalles_orden/index.html.twig', [
            'controller_name' => 'DetallesOrdenController',
        ]);
    }
}
