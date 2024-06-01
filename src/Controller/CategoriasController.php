<?php

namespace App\Controller;

use App\Entity\Categorias;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use App\Service\VerificarRol;
use App\Service\CategoriasService;
use Symfony\Component\HttpFoundation\Response;



class CategoriasController extends AbstractController
{
    private $verificarRol;
    private $session;
    private $entityManager;
    private $categoriaService;
    

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificarRol $verificarRol,
        CategoriasService $categoriaService
    ) {
        $this->entityManager = $entityManager;
        $this->verificarRol = $verificarRol;
        $this->categoriaService =$categoriaService;
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
        if(!$session->isStarted()){
            return new JsonResponse(['error' => 'Aun no has iniciado sesion'], Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED);
        }

      // Obtener el correo electrónico almacenado en la sesión
          $admin_email = $session->get('user_email');
      
          if (!$this->verificarRol->isAdmin($admin_email)) {
              return new JsonResponse(['error' => 'No sos administrador'], Response::HTTP_FORBIDDEN);
          }
      
          // Utiliza el servicio de categoría para crear una nueva categoría
          return $this->categoriaService->crearCategoria($request);
    }
}
