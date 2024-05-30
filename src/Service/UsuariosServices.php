<?php
namespace App\Service;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $usuariosRepository;
    private $entityManager;

    public function __construct(UsuariosRepository $usuariosRepository, EntityManagerInterface $entityManager)
    {
        $this->usuariosRepository = $usuariosRepository;
        $this->entityManager = $entityManager;
    }

    public function deleteUser($adminEmail, $emailUsuario)
    {
        // Verificar si el usuario en la sesión tiene el rol de administrador
        $admin = $this->usuariosRepository->findOneByEmail($adminEmail);
        if (!in_array('ROLE_ADMIN', $admin->getRoles())) {
            return ['error' => 'No sos administrador'];
        }

        // Buscar el usuario por su correo electrónico
        $user = $this->usuariosRepository->findOneByEmail($emailUsuario);
        if (!$user) {
            return ['error' => 'Usuario no encontrado'];
        }

        // Eliminar el usuario
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return ['message' => 'Usuario eliminado correctamente'];
    }


    
}
