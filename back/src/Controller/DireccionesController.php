<?php

namespace App\Controller;

use App\Repository\DireccionesRepository;
use App\Repository\UsuariosRepository;
use App\Service\UsuariosServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class DireccionesController extends AbstractController
{

    private $usuariosRepository;
    private $usuariosServices;
    private $direccionesRepository;

    public function __construct(UsuariosRepository $usuariosRepository,
    DireccionesRepository $direccionesRepository,
    UsuariosServices $usuariosServices
    )
    {
        $this->usuariosRepository = $usuariosRepository;
        $this->usuariosServices = $usuariosServices;

    }


    #[Route('/direcciones', name: 'app_direcciones')]
    public function index(): Response
    {
        return $this->render('direcciones/index.html.twig', [
            'controller_name' => 'DireccionesController',
        ]);
    }


    #[Route('/modificar-info-user', name: 'modificar_info_user')]
    public function modificarUser(
        SessionInterface $session,
        Request $request,
    ): Response {

        if(!$session->isStarted()){
            return new JsonResponse(['error' => 'Aun no has iniciado sesion'], Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED);
        }
        
        $email = $session->get('user_email');

        $user = $this->usuariosRepository->findOneByEmail($email);

        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        

        

    }
}
