<?php

namespace App\Controller;

use App\Entity\Usuarios;
use App\Repository\UsuariosRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{



    #[Route('/security', name: 'app_security')]
    public function index(): Response
    {
        return $this->render('security/index.html.twig', [
            'controller_name' => 'SecurityController',
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(
        Request $request,
        UsuariosRepository $usuariosRepository,
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface $session
        
    ): Response {

        // Decodifica el JSPON
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Si el correo o contraseña están vacios
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Correo electrónico o contraseña no proporcionados'], Response::HTTP_BAD_REQUEST);
        }

        $user = $usuariosRepository->findOneByEmail($email);

        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Contraseña incorrecta'], Response::HTTP_UNAUTHORIZED);
        }

        $session->set('user_email', $user->getEmail());

        // Ahora, dependiendo del rol del usuario, redirigirlo a diferentes rutas
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            // Si es administrador, redirigir al panel administrativo
            // Puedes cambiar 'admin_dashboard' a la ruta real de tu panel administrativo
            return new JsonResponse(['redirect_to' => 'admin_dashboard'], Response::HTTP_OK);
        } else {
            // Si es usuario normal, redirigir al panel de usuario
            // Puedes cambiar 'user_profile' a la ruta real de tu panel de usuario
            return new JsonResponse(['redirect_to' => 'user_profile'], Response::HTTP_OK);
        }
    }
}



/**authenticateAction(): Este método podría manejar el proceso de autenticación de los usuarios, recibiendo las credenciales del usuario (como el nombre de usuario y la contraseña) y devolviendo un token de autenticación JWT u otro tipo de token.

refreshTokenAction(): Si estás utilizando tokens de acceso JWT que expiran después de un cierto período de tiempo, este método podría manejar las solicitudes para refrescar esos tokens, proporcionando un nuevo token de acceso válido.

forgotPasswordAction(): Similar a como se mencionó anteriormente, este método podría manejar la solicitud de restablecimiento de contraseña enviando un correo electrónico al usuario con un enlace para restablecer su contraseña.

resetPasswordAction(): Procesaría la solicitud de restablecimiento de contraseña, permitiendo al usuario establecer una nueva contraseña.

registerAction(): Si tu API permite el registro de nuevos usuarios, este método manejaría el proceso de registro y creación de nuevos usuarios.

changePasswordAction(): Permite a los usuarios cambiar su contraseña actual mediante una solicitud a la API.

deleteUserAction(): Este método permitiría a un administrador eliminar un usuario existente de la base de datos.
//LOGOUT SOLO QUE INVALIDE EL TOKEN DEL USUARIO
updateUserRoleAction(): Si necesitas cambiar los roles de un usuario (por ejemplo, asignarle el rol de administrador), este método podría manejar esa operación. */
