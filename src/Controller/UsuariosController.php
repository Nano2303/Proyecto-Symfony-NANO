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

class UsuariosController extends AbstractController
{
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
        UsuariosRepository $usuariosRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {


        // Obtener el correo electrónico almacenado en la sesión
        $admin_email = $session->get('user_email');

        $admin = $usuariosRepository->findOneByEmail($admin_email);

        // Verificar si el usuario en la sesión tiene el rol de administrador
        if (!in_array('ROLE_ADMIN',$admin->getRoles())) {
            // lo enviaria al home con su usuario normal
            return new JsonResponse(['error' => 'No sos administrador'], Response::HTTP_NOT_FOUND); //pa que se salga directamente de aca
        }
        
        $data = json_decode($request->getContent(),true);


        
        $email_usuario =$data['email'] ?? null;
        if (!$email_usuario) {
            return new JsonResponse(['error' => 'El campo email es necesario'], Response::HTTP_NOT_ACCEPTABLE);
        }
        $user = $usuariosRepository->findOneByEmail($email_usuario);

        if(!$user){
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }


        $entityManager->remove($user);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Usuario eliminado correctamente'], Response::HTTP_OK);
    
    }


   
    
}
