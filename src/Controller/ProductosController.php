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

class ProductosController extends AbstractController
{
    private $categoriasRepository;
    private $usuariosRepository;
    private $entityManager;

    public function __construct(
        UsuariosRepository $usuariosRepository,
        EntityManagerInterface $entityManager,
        CategoriasRepository $categoriasRepository
    ) {
        $this->usuariosRepository = $usuariosRepository;
        $this->entityManager = $entityManager;
        $this->categoriasRepository = $categoriasRepository;
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
        // Obtener el correo electrónico almacenado en la sesión
        $admin_email = $session->get('user_email');

        $admin = $this->usuariosRepository->findOneByEmail($admin_email);

        // Verificar si el usuario en la sesión tiene el rol de administrador
        if (!in_array('ROLE_ADMIN', $admin->getRoles())) {
            // lo enviaria al home con su usuario normal
            return new JsonResponse(['error' => 'No sos administrador'], Response::HTTP_NOT_FOUND); //pa que se salga directamente de aca
        }
        $data = json_decode($request->getContent(), true);

        $categoria_id = $data['categoria_id'] ?? null;

        if(!$categoria_id){
            return new JsonResponse(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        $categoria = $this->categoriasRepository->find($categoria_id);
        
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


