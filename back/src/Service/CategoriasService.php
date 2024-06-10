<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response; // Agrega esta línea para importar la clase Response
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UsuariosRepository;
use App\Entity\Categorias;
use App\Repository\CategoriasRepository;

class CategoriasService{




 private $entityManager;
 private $usuariosRepository;
 private $categoriaRepository;
 
    public function __construct(
        UsuariosRepository $usuariosRepository,
        EntityManagerInterface $entityManager,
        CategoriasRepository $categoriaRepository
        )
    {
        $this->entityManager = $entityManager;
        $this->usuariosRepository = $usuariosRepository;
        $this->categoriaRepository = $categoriaRepository;

    }
    public function getCategorias(
        ):Response {
    
            $categorias = $this->categoriaRepository->findAll();
            $categoriasArray = [];
    
            foreach ($categorias as $categoria) {
                $categoriasArray[] = [
                    'id' => $categoria->getId(),
                    'nombre' => $categoria->getNombre(),
                    'descripcion' => $categoria->getDescripcion(),
                ];
            }
    
            return new JsonResponse(['categorias' => $categoriasArray]);
        
        }

    public function crearCategoria(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $nombre_categoria = $data['nombre'] ?? null;
        $descripcion_categoria = $data['descripcion'] ?? null;

        if (!$nombre_categoria || !$descripcion_categoria) {
            return new JsonResponse(['error' => 'Los campos nombre y descripcion son necesarios'], Response::HTTP_BAD_REQUEST);
        }

        $categoria = new Categorias();
        $categoria->setNombre($nombre_categoria);
        $categoria->setDescripcion($descripcion_categoria);

        $this->entityManager->persist($categoria);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Categoría ' . $nombre_categoria . ' creada correctamente'], Response::HTTP_OK);
    }


    public function getProductosCategoria(Request $request):JsonResponse
    {
        $data = json_decode($request->getContent(), true);

    $id_categoria = $data['id'] ?? null;

    if (!$id_categoria) {
        return new JsonResponse(['error' => 'Campo id necesario'], Response::HTTP_NO_CONTENT);
    }

    $categoria = $this->categoriaRepository->find($id_categoria);

    if (!$categoria) {
        return new JsonResponse(['error' => 'No se ha encontrado ninguna categoria con ese id'], Response::HTTP_NOT_FOUND);
    }

    $productos = $categoria->getProductos();
    error_log('Productos encontrados: ' . count($productos));

    if (count($productos) === 0) {
        return new JsonResponse(['error' => 'No hay productos en esta categoría'], Response::HTTP_NOT_FOUND);
    }

    $datosProductos = [];
    foreach ($productos as $producto) {
        error_log('Producto encontrado: ID = ' . $producto->getId() . ', Nombre = ' . $producto->getNombre());

        $datosProductos[] = [
            'id' => $producto->getId(),
            'categorias_id' => $producto->getCategorias() ? $producto->getCategorias()->getId() : null,
            'nombre' => $producto->getNombre(),
            'descripcion' => $producto->getDescripcion(),
            'precio' => $producto->getPrecio(),
            'talla' => $producto->getTalla(),
            'color' => $producto->getColor(),
            'cantidad_inventario' => $producto->getCantidadInventario(),
            'src' => $producto->getSrc()
        ];
    }

    return new JsonResponse(['productos' => $datosProductos]);
    }


}