<?php

namespace App\Controller;

use App\Entity\Productos;
use App\Repository\CategoriasRepository;
use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\VerificarRol;
use App\Service\ProductosService;

class ProductosController extends AbstractController
{
    private $verificarRol;
    private $session;
    private $usuariosRepository;
    private $entityManager;
    private $categoriaRepository;
    private $productosService;

    public function __construct(
        UsuariosRepository $usuariosRepository,
        EntityManagerInterface $entityManager,
        VerificarRol $verificarRol,
        CategoriasRepository $categoriaRepository,
        ProductosService $productosService
    ) {
        $this->usuariosRepository = $usuariosRepository;
        $this->entityManager = $entityManager;
        $this->verificarRol = $verificarRol;
        $this->categoriaRepository = $categoriaRepository;
        $this->productosService = $productosService;
        

    }

    #[Route('/productos', name: 'app_productos')]
    public function index(): Response
    {
        return $this->render('productos/index.html.twig', [
            'controller_name' => 'ProductosController',
        ]);
    }


    #[Route('/crear-productos', name: 'crear_productos')]
    public function crearProductos(
        Request $request,
        SessionInterface $session
    ): Response {

        if(!$session->isStarted()){
            return new JsonResponse(['error' => 'Aun no has iniciado sesion'], Response::HTTP_NOT_FOUND);
        }
        $admin_email = $session->get('user_email');

        if (!$this->verificarRol->isAdmin($admin_email)) {
            return new JsonResponse(['error' => 'No sos administrador'], Response::HTTP_NOT_FOUND); //pa que se salga directamente de aca
        }

        return $this->productosService->crearProducto($request);
    }
}





