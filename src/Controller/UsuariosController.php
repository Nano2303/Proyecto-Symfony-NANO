<?php

namespace App\Controller;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\VerificarRol;
use App\Service\UsuariosServices;


class UsuariosController extends AbstractController
{
    private $verificarRol;
    private $session;
    private $entityManager;
    private $usuariosServices;

    public function __construct(
        EntityManagerInterface $entityManager,
        VerificarRol $verificarRol,
        UsuariosServices $usuariosServices
    ) {
        $this->entityManager = $entityManager;
        $this->verificarRol = $verificarRol;
        $this->usuariosServices = $usuariosServices;
    }


    #[Route('/usuarios', name: 'app_usuarios',methods: ['DELETE'])]
    public function index(): Response
    {
        return $this->render('usuarios/index.html.twig', [
            'controller_name' => 'UsuariosController',
        ]);
    }



    #[Route('/delete-user', name: 'delete_user')]
    public function deleteUser(
        SessionInterface $session,
        Request $request,
    ): Response {
        $admin_email = $session->get('user_email');
        
        if (!$this->verificarRol->isAdmin($admin_email)) {
            return new JsonResponse(['error' => 'No sos administrador'], Response::HTTP_NOT_FOUND); //pa que se salga directamente de aca
        }

        $data = json_decode($request->getContent(),true);

        $email_usuario =$data['email'] ?? null;
       
        return $this->usuariosServices->deleteUser($admin_email, $email_usuario);
    }


   
    
}
