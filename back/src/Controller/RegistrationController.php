<?php

namespace App\Controller;

use App\Entity\Direcciones;
use App\Entity\Usuarios;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'app_registration')]
    public function index(): Response
    {
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
        ]);
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);


        $user = $serializer->deserialize(json_encode($data['usuario']), Usuarios::class, 'json');

        $direccionData = $data['direccion'];
        $direccion = new Direcciones();
        $direccion->setCalle($direccionData['calle']);
        $direccion->setCiudad($direccionData['ciudad']);
        $direccion->setProvincia($direccionData['provincia']);
        $direccion->setCodigoPostal($direccionData['codigo_postal']);
        $direccion->setPais($direccionData['pais']);

        // Validamos direccion
        $errors = $validator->validate($user);
        $errors->addAll($validator->validate($direccion));
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        //hasheo de la contraseña
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        // Aprovechamos la funcion adddireciones de la entidad usuario
        $user->addDireccione($direccion);
        $user->setRoles(["ROLE_USER"]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Usuario registrado exitosamente'], Response::HTTP_CREATED);
    }
}
