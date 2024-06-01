<?php
namespace App\Service;

use App\Repository\UsuariosRepository;

class VerificarRol
{

private $usuariosRepository;
public function __construct(UsuariosRepository $usuariosRepository)
{
    $this->usuariosRepository = $usuariosRepository;
}


public function isAdmin($admin_email):bool
{
    $admin = $this->usuariosRepository->findOneByEmail($admin_email);

    if (!in_array('ROLE_ADMIN', $admin->getRoles())){ return false;}

    return true;
}




}