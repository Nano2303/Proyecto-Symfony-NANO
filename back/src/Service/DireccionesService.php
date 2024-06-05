<?php
namespace App\Service;

use App\Repository\CategoriasRepository;
use App\Repository\DireccionesRepository;
use App\Repository\UsuariosRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response; // Agrega esta línea para importar la clase Response
use Doctrine\ORM\EntityManagerInterface;


class DireccionesService{

    private $entityManager;
    private $usuariosRepository;
    private $direccionesRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UsuariosRepository $usuariosRepository,
        DireccionesRepository $direccionesRepository
    ) {
        $this->entityManager = $entityManager;
        $this->usuariosRepository =$usuariosRepository;
        $this->direccionesRepository = $direccionesRepository;
    }


    
   
}