<?php
namespace App\Service;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsuariosServices
{
    private $usuariosRepository;
    private $entityManager;

    public function __construct(UsuariosRepository $usuariosRepository, EntityManagerInterface $entityManager)
    {
        $this->usuariosRepository = $usuariosRepository;
        $this->entityManager = $entityManager;
    }

    public function deleteUser($adminEmail, $email_usuario):Response
    {
        if (!$email_usuario) {
            return new JsonResponse(['error' => 'El campo email es necesario'], Response::HTTP_NOT_ACCEPTABLE);
        }
        $user = $this->usuariosRepository->findOneByEmail($email_usuario);

        if(!$user){
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }


        $this->entityManager->remove($user);
        $this->entityManager->flush();
    
        return new JsonResponse(['message' => 'Usuario eliminado correctamente'], Response::HTTP_OK);
    }


    
}
