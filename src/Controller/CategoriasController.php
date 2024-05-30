<?php

namespace App\Controller;

use App\Entity\Categorias;
use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class CategoriasController extends AbstractController
{

    private $session;
    private $usuariosRepository;
    private $entityManager;

    public function __construct(
        UsuariosRepository $usuariosRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->usuariosRepository = $usuariosRepository;
        $this->entityManager = $entityManager;
    }


    #[Route('/categorias', name: 'app_categorias')]
    public function index(): Response
    {
        return $this->render('categorias/index.html.twig', [
            'controller_name' => 'CategoriasController',
        ]);
    }

    #[Route('/crear-categoria', name: 'crear_categoria')]
    public function crearCategoria(
        Request $request,
        SessionInterface $session
    ): Response
    {
        // Obtener el correo electrónico almacenado en la sesión
        $admin_email = $session->get('user_email');

        $admin = $this->usuariosRepository->findOneByEmail($admin_email);

        // Verificar si el usuario en la sesión tiene el rol de administrador
        if (!in_array('ROLE_ADMIN',$admin->getRoles())) {
            // lo enviaria al home con su usuario normal
            return new JsonResponse(['error' => 'No sos administrador'], Response::HTTP_NOT_FOUND); //pa que se salga directamente de aca
        }
        $data = json_decode($request->getContent(),true);

        $nombre_categoria = $data['nombre'] ?? null;
        $descripcion_categoria = $data['descripcion'] ?? null;

        if(!$nombre_categoria || !$descripcion_categoria){
            return new JsonResponse(['error' => 'Los campos nombre y descripcion son necesarios'], Response::HTTP_NOT_ACCEPTABLE);
        }

        $categoria = new Categorias();
        $categoria->setNombre($nombre_categoria);
        $categoria->setDescripcion($descripcion_categoria);

        $this->entityManager->persist($categoria);
        $this->entityManager->flush();

        return new JsonResponse(['error' => 'Categoria ' .$nombre_categoria. ' Creada correctamente '], Response::HTTP_OK);

    
    }
}
