<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrdenesController extends AbstractController
{
    #[Route('/ordenes', name: 'app_ordenes')]
    public function index(): Response
    {
        return $this->render('ordenes/index.html.twig', [
            'controller_name' => 'OrdenesController',
        ]);
    }
}
