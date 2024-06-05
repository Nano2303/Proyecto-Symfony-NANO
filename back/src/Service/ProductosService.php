<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductosRepository;
use App\Repository\CategoriasRepository;

use App\Entity\Productos;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class ProductosService
{
    private $entityManager;
    private $productosRepository;
    private $categoriaRepository;

    public function __construct(
        ProductosRepository $productosRepository,
        EntityManagerInterface $entityManager,
        CategoriasRepository $categoriaRepository
    ) {
        $this->entityManager = $entityManager;
        $this->productosRepository = $productosRepository;
        $this->categoriaRepository = $categoriaRepository;
    }

    public function crearProducto(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $categoria_id = $data['categoria_id'] ?? null;

        if (!$categoria_id) {
            return new JsonResponse(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        $categoria = $this->categoriaRepository->find($categoria_id);

        if (!$categoria) {
            return new JsonResponse(['error' => 'No existe esta categoria'], Response::HTTP_NOT_FOUND);
        }

        $nombre = $data['nombre'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $precio = $data['precio'] ?? null;
        $talla = $data['talla'] ?? null;
        $color = $data['color'] ?? null;
        $cantidad_inventario = $data['cantidad_inventario'] ?? null;
        $src = $data['src'] ?? null;

        if (!$nombre || !$descripcion || !$precio || !$talla || !$color || !$cantidad_inventario || !$src) {
            return new JsonResponse(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        $producto = new Productos();
        $producto->setCategorias($categoria);
        $producto->setNombre($nombre);
        $producto->setDescripcion($descripcion);
        $producto->setPrecio($precio);
        $producto->setTalla($talla);
        $producto->setColor($color);
        $producto->setCantidadInventario($cantidad_inventario);
        $producto->setSrc($src);

        $this->entityManager->persist($producto);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Producto creado correctamente'], Response::HTTP_CREATED);
    }

    public function reponerProductos(Request $request)
    {

        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $cantidadReponer = $data['cantidad'];

        $producto = $this->productosRepository->find($id);

        if (!$id || !$cantidadReponer) {
            return new JsonResponse(['error' => 'Campos id y cantidad requeridos'], Response::HTTP_NOT_FOUND);
        }

        if (!$producto) {
            return new JsonResponse(['error' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $cantidad = $producto->getCantidadInventario();

        $nuevaCantidad = $cantidadReponer + $cantidad;
        $producto->setCantidadInventario($nuevaCantidad);

        $this->entityManager->flush();

        return new JsonResponse(["Mensaje" => " Usted ha ingresado una cantidad a reponer de " . $cantidadReponer . " \n Cantidad antigua => " . $cantidad . "\n Cantidad Nueva => " . $nuevaCantidad]);
    }


    public function getListaProductos()
    {
        $productos = $this->productosRepository->findAll();

        // Convertir los objetos de producto en un array asociativo
        $datosProductos = [];
        foreach ($productos as $producto) {
            $datosProductos[] = [
                'id' => $producto->getId(),
                'categorias_id' => $producto->getCategorias(),
                'nombre' => $producto->getNombre(),
                'descripcion' =>$producto->getDescripcion(),
                'precio'=>$producto->getPrecio(),
                'talla'=>$producto->getTalla(),
                'color'=>$producto->getColor(),
                'cantidad_inventario'=>$producto->getCantidadInventario(),
                'src'=>$producto->getSrc()
                // Agrega otras propiedades del producto según sea necesario
            ];
        }
        return $datosProductos;
    }


    public function borrarProducto(Request $request)
    {
        $data = json_decode($request->getContent(),true);

        $id_producto = $data['id'] ?? null;

        if(!$id_producto){
            return new JsonResponse(["Error"=>" Campo id necesario"]);
        }

        $producto = $this->productosRepository->find($id_producto);

        if(!$producto){
            return new JsonResponse(["Error"=>" No existe producto con el id proporcionado"]);
        }

        $this->entityManager->remove($producto);
        $this->entityManager->flush();

        return new JsonResponse(["Mensaje "=>"Producto eliminado con exito"]);

    }
}
