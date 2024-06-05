<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UsuariosRepository;

class CarritoComprasController extends AbstractController
{
    private $usuariosRepository;

    public function __construct(UsuariosRepository $usuariosRepository)
    {
        $this->usuariosRepository=$usuariosRepository;

    }


    #[Route('/carrito/compras', name: 'app_carrito_compras')]
    public function index(): Response
    {
        return $this->render('carrito_compras/index.html.twig', [
            'controller_name' => 'CarritoComprasController',
        ]);
    }


   
}
