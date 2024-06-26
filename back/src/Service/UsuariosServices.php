<?php
namespace App\Service;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Direcciones;
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

    public function getInfoUserActual($email_usuario){

        $user = $this->usuariosRepository->findOneByEmail($email_usuario);
        if(!$user){
            return new JsonResponse(['error' => 'No se encontró ningún usuario con ese correo electrónico'], Response::HTTP_NOT_FOUND);
        }
        $direccion = $user->getDirecciones();
        foreach ($user->getDirecciones() as $direccion) {
            $direcciones[] = [
                'calle' => $direccion->getCalle(),
                'ciudad' => $direccion->getCiudad(),
                'provincia' => $direccion->getProvincia(),
                'codigo_postal' => $direccion->getCodigoPostal(),
                'pais' => $direccion->getPais(),
            ];
        }
        $userData = [
            'nombre' => $user->getNombre(),
            'email' => $user->getEmail(),
            'telefono' => $user->getTelefono(),
            'direcciones' => $direcciones,
        ];
    
    
        return new JsonResponse($userData);
    }

    
}
