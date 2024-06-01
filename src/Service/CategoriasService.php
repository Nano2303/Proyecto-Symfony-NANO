<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response; // Agrega esta línea para importar la clase Response
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UsuariosRepository;
use App\Entity\Categorias;

class CategoriasService{




 private $entityManager;
 private $usuariosRepository;

    public function __construct(
        UsuariosRepository $usuariosRepository,
        EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->usuariosRepository = $usuariosRepository;

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




}