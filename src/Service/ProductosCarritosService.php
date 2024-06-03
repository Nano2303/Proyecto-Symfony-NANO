<?php

namespace App\Service;

use App\Entity\CarritoCompras;
use App\Entity\ProductosCarrito;
use App\Repository\CarritoComprasRepository;
use App\Repository\UsuariosRepository;
use App\Repository\CarritosRepository;
use App\Repository\ProductosCarritoRepository;
use App\Repository\ProductosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductosCarritosService
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



    public function agregarProductoCarrito(Request $request, $usuario): Response
    {

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
        }else if ($producto->getCantidadInventario()<$cantidad){
            return new JsonResponse(['error' => 'No hay cantidad suficiente'], Response::HTTP_NOT_ACCEPTABLE);
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

        $producto->setCantidadInventario($producto->getCantidadInventario() - $cantidad);

        $this->entityManager->persist($producto);
        $this->entityManager->persist($carrito);
        $this->entityManager->flush();

        return new JsonResponse(['Mensaje' => 'Pedido agregado al carrito con exito'], Response::HTTP_OK);

    }


    public function borrarProductoCarrito(Request $request, $usuario): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $carrito_por_user_id = $this->carritoComprasRepository->findByUserId($usuario->getId());
        $productos_id = $data['productos_id'] ?? null;
    
        if (!$productos_id) {
            return new JsonResponse(['error' => 'ID del producto no proporcionado'], Response::HTTP_BAD_REQUEST);
        }
    
        if (!$carrito_por_user_id) {
            return new JsonResponse(['error' => 'Carrito no encontrado para el usuario'], Response::HTTP_NOT_FOUND);
        } else {
            $carrito = $this->carritoComprasRepository->find($carrito_por_user_id);
        }
    
        $producto = $this->productosRepository->find($productos_id);
    
        if (!$producto) {
            return new JsonResponse(['error' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }
    
        $productoCarrito = $this->productosCarritoRepository->findOneBy([
            'carritoCompras' => $carrito,
            'productos' => $producto
        ]);
    
        if (!$productoCarrito) {
            return new JsonResponse(['error' => 'Producto no encontrado en el carrito'], Response::HTTP_NOT_FOUND);
        }
    
        // Eliminar el producto del carrito
        $cantidad = $productoCarrito->getCantidad();
        $total = $producto->getPrecio() * $cantidad;
    
        $this->entityManager->remove($productoCarrito);
        $carrito->setTotal($carrito->getTotal() - $total);
    
        // Actualizar la cantidad de inventario del producto
        $producto->setCantidadInventario($producto->getCantidadInventario() + $cantidad);
    
        $this->entityManager->persist($producto);
        $this->entityManager->persist($carrito);
        $this->entityManager->flush();
    
        return new JsonResponse(['Mensaje' => 'Producto eliminado del carrito con éxito'], Response::HTTP_OK);
    }



}