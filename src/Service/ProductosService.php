<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductosRepository;
use App\Repository\CategoriasRepository;

use App\Entity\Productos;

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

        if(!$categoria_id){
            return new JsonResponse(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        $categoria = $this->categoriaRepository->find($categoria_id);
        
        if(!$categoria){
            return new JsonResponse(['error' => 'No existe esta categoria'], Response::HTTP_NOT_FOUND);
        }
        
        $nombre = $data['nombre'] ?? null;
        $descripcion = $data['descripcion'] ?? null;
        $precio = $data['precio'] ?? null;
        $talla = $data['talla'] ?? null;
        $color = $data['color'] ?? null;
        $cantidad_inventario = $data['cantidad_inventario'] ?? null;

        if (!$nombre || !$descripcion || !$precio || !$talla || !$color || !$cantidad_inventario) {
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

        $this->entityManager->persist($producto);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Producto creado correctamente'], Response::HTTP_CREATED);
    }

}
