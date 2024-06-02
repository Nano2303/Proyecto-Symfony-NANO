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

class ProductosCarritosController extends AbstractController
{

    private $usuariosRepository;
    private $productosRepository;
    private $carritoComprasRepository;
    private $entityManager;
    private $productosCarritoRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UsuariosRepository $usuariosRepository,
        ProductosRepository $productosRepository,
        CarritoComprasRepository $carritoComprasRepository,
        ProductosCarritoRepository $productosCarritoRepository
    ) {
        $this->entityManager = $entityManager;
        $this->usuariosRepository = $usuariosRepository;
        $this->productosRepository = $productosRepository;
        $this->carritoComprasRepository = $carritoComprasRepository;
        $this->productosCarritoRepository = $productosCarritoRepository;
    }

    #[Route('/productos/carritos', name: 'app_productos_carritos')]
    public function index(): Response
    {
        return $this->render('productos_carritos/index.html.twig', [
            'controller_name' => 'ProductosCarritosController',
        ]);
    }


    #[Route('/carrito/agregar-producto', name: 'carrito_agregar_producto', methods: ['POST'])]
    public function agregarProducto(Request $request, Session $session): Response
    //Request tiene que tener el id carrito de compras y el id del producto y cantidad
    {
        if (!$session->has('user_email')) {
            return new JsonResponse(['error' => 'Aun no has iniciado sesion'], Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED);
        }

        $usuario = $this->usuariosRepository->findOneByEmail($session->get('user_email'));

        if (!$usuario) {
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $carrito_por_user_id = $this->carritoComprasRepository->findByUserId($usuario->getId());


        $productos_id = $data['productos_id'] ?? null;
        $cantidad = $data['cantidad'] ?? null; //Entero todos

        if (!$productos_id || !$cantidad) {
            return new JsonResponse(['error' => 'Tienes que proporcionar todos los datos '], Response::HTTP_NOT_IMPLEMENTED);
        }
        if (!$carrito_por_user_id) {
            $carrito = new CarritoCompras();
            $carrito->setUsuarios($usuario);
        }else {
            $carrito = $this->carritoComprasRepository->find($carrito_por_user_id);
        }

        $producto = $this->productosRepository->find($productos_id);

        if (!$producto) {
            return new JsonResponse(['error' => 'No se ha encontrado este producto'], Response::HTTP_NOT_FOUND);
        }

        

        $productosCarritoExistente = $this->productosCarritoRepository->findOneBy([
            'carritoCompras' => $carrito,
            'productos' => $producto
        ]);

        if ($productosCarritoExistente) {
            // Si el producto ya existe en el carrito, actualiza la cantidad
            $productosCarritoExistente->setCantidad($productosCarritoExistente->getCantidad() + $cantidad);

            $total = $producto->getPrecio() * $cantidad;

            $carrito->setTotal($carrito->getTotal() + $total);
        } else {
            // Si el producto no existe en el carrito, crea uno nuevo
            $carritoCompras  = new ProductosCarrito();
            $carritoCompras->setCarritoCompras($carrito);
            $carritoCompras->setProductos($producto);
            $carritoCompras->setCantidad($cantidad);
            $this->entityManager->persist($carritoCompras);

            $total = $producto->getPrecio() * $cantidad;

            $carrito->setTotal($carrito->getTotal() + $total);

            $this->entityManager->persist($carritoCompras);
        }


        $this->entityManager->persist($carrito);
        $this->entityManager->flush();

        return new JsonResponse(['Mensaje' => 'Pedido agregado al carrito con exito'], Response::HTTP_OK);
    }
}
