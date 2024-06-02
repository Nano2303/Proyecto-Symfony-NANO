<?php

namespace App\Controller;

use App\Entity\CarritoCompras;
use App\Entity\ProductosCarrito;
use App\Repository\CarritoComprasRepository;
use App\Repository\UsuariosRepository;
use App\Repository\CarritosRepository;
use App\Repository\ProductosCarritoRepository;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\ProductosCarritosService;

class ProductosCarritosController extends AbstractController
{

    private $usuariosRepository;
    private $productosRepository;
    private $carritoComprasRepository;
    private $entityManager;
    private $productosCarritoRepository;
    private $productosCarritosService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UsuariosRepository $usuariosRepository,
        ProductosRepository $productosRepository,
        CarritoComprasRepository $carritoComprasRepository,
        ProductosCarritoRepository $productosCarritoRepository,
        ProductosCarritosService $productosCarritosService
    ) {
        $this->entityManager = $entityManager;
        $this->usuariosRepository = $usuariosRepository;
        $this->productosRepository = $productosRepository;
        $this->carritoComprasRepository = $carritoComprasRepository;
        $this->productosCarritoRepository = $productosCarritoRepository;
        $this->productosCarritosService = $productosCarritosService;
    }

    #[Route('/productos/carritos', name: 'app_productos_carritos')]
    public function index(): Response
    {
        return $this->render('productos_carritos/index.html.twig', [
            'controller_name' => 'ProductosCarritosController',
        ]);
    }


    #[Route('/carrito/agregar-producto', name: 'carrito_agregar_producto', methods: ['POST'])]
    public function agregarProductoCarrito(Request $request, Session $session): Response
    //Request tiene que tener el id carrito de compras y el id del producto y cantidad
    {
        if (!$session->has('user_email')) {
            return new JsonResponse(['error' => 'Aun no has iniciado sesion'], Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED);
        }

        $usuario = $this->usuariosRepository->findOneByEmail($session->get('user_email'));

        if (!$usuario) {
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_FOUND);
        }

        return $this->productosCarritosService->agregarProductoCarrito($request,$usuario);
        
    }
}
/**
 */