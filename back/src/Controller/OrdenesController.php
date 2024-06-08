<?php

namespace App\Controller;

use App\Entity\CarritoCompras;
use App\Entity\DetallesOrden;
use App\Entity\Ordenes;
use App\Entity\Pagos;
use App\Entity\Usuarios;
use App\Repository\UsuariosRepository;
use App\Repository\CarritoComprasRepository;
use App\Repository\DireccionesRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class OrdenesController extends AbstractController
{
    private $entityManager;
    private $usuariosRepository;
    private $carritoComprasRepository;
    private $direccionesRepository;
    private $emailService;
    public function __construct(
        EntityManagerInterface $entityManager,
        UsuariosRepository $usuariosRepository,
        CarritoComprasRepository $carritoComprasRepository,
        DireccionesRepository $direccionesRepository,
        EmailService $emailService
    ) {
        $this->entityManager = $entityManager;
        $this->usuariosRepository = $usuariosRepository;
        $this->carritoComprasRepository = $carritoComprasRepository;
        $this->direccionesRepository = $direccionesRepository;
        $this->emailService = $emailService;
    }

    #[Route('/ordenes', name: 'app_ordenes')]
    public function index(): Response
    {
        return $this->render('ordenes/index.html.twig', [
            'controller_name' => 'OrdenesController',
        ]);
    }

    #[Route('/crear-orden', name: 'crear_ordene')]
    public function createOrder(Request $request, SessionInterface $session)
    {
        $usuario = $this->usuariosRepository->findOneByEmail($session->get('user_email'));
        $precioFinal = $this->carritoComprasRepository->findByUserId($usuario->getId())->getTotal();
        
        if (!$usuario) {
            return new JsonResponse(['error' => 'No se encontró el usuario con el email proporcionado.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        if ($usuario) {
            $pago = new Pagos();
            $pago->setFecha(new \DateTime());
            $pago->setMonto($precioFinal);
            $pago->setMetodoPago('PayPal');
    
            $this->entityManager->persist($pago);
            $this->entityManager->flush();
    
            $direcciones = $usuario->getDirecciones();
        
            if (!$direcciones || count($direcciones) === 0) {
                return new JsonResponse(['error' => 'No se encontró una dirección guardada para el usuario.'], JsonResponse::HTTP_BAD_REQUEST);
            }
    
            $direccion = $direcciones[0];
    
            $orden = new Ordenes();
            $orden->setFecha((new \DateTime())->format('Y-m-d H:i:s'));
            $orden->setUsuarios($usuario);
            $orden->setDireccionEnvio($direccion->getCalle() . ', ' . $direccion->getCiudad() . ', ' . $direccion->getProvincia() . ', ' . $direccion->getCodigoPostal() . ', ' . $direccion->getPais());
            $orden->setPagos($pago);
            $orden->setEstado('Pendiente');
            
            $totalOrden = 0;
    
            $carrito = $this->carritoComprasRepository->findOneBy(['usuarios' => $usuario]);
    
            if (!$carrito) {
                return new JsonResponse(['error' => 'No se encontró un carrito de compras para el usuario.'], JsonResponse::HTTP_BAD_REQUEST);
            }
    
            $jsonOrden = [
                'fecha' => (new \DateTime())->format('Y-m-d H:i:s'),
                'usuario' => [
                    'email' => $usuario->getEmail(),
                    'nombre' => $usuario->getNombre(),
                ],
                'direccion_envio' => [
                    'calle' => $direccion->getCalle(),
                    'ciudad' => $direccion->getCiudad(),
                    'provincia' => $direccion->getProvincia(),
                    'codigo_postal' => $direccion->getCodigoPostal(),
                    'pais' => $direccion->getPais(),
                ],
                'pago' => [
                    'monto' => $precioFinal,
                    'metodo' => 'PayPal',
                ],
                'productos' => [],
                'estado' => 'Pendiente',
                'total' => 0,
            ];
    
            foreach ($carrito->getProductosCarrito() as $productoCarrito) {
                $detalleOrden = new DetallesOrden();
                $detalleOrden->setCantidad($productoCarrito->getCantidad());
                $detalleOrden->setPrecioUnitario($productoCarrito->getProductos()->getPrecio());
                $detalleOrden->setOrdenes($orden);
                $detalleOrden->setProductos($productoCarrito->getProductos());
    
                $this->entityManager->persist($detalleOrden);
    
                $totalOrden += $productoCarrito->getCantidad() * $productoCarrito->getProductos()->getPrecio();
    
                $jsonOrden['productos'][] = [
                    'nombre' => $productoCarrito->getProductos()->getNombre(),
                    'cantidad' => $productoCarrito->getCantidad(),
                    'precio_unitario' => $productoCarrito->getProductos()->getPrecio(),
                    'subtotal' => $productoCarrito->getCantidad() * $productoCarrito->getProductos()->getPrecio(),
                ];
    
                $this->entityManager->remove($productoCarrito);
            }
    
            $orden->setTotal($totalOrden);
            $jsonOrden['total'] = $totalOrden;
            $carrito->setTotal(0);
    
            $this->entityManager->persist($orden);
            $this->entityManager->persist($carrito);
            $this->entityManager->flush();

            $this->emailService->enviarDetallesDeOrden($jsonOrden);
            return new JsonResponse($jsonOrden);
        } else {
            return new JsonResponse(['error' => 'Error en el pago con PayPal.']);
        }
    }
    

}

/**<?php

namespace App\Controller;

use App\Entity\CarritoCompras;
use App\Entity\DetallesOrden;
use App\Entity\Direcciones;
use App\Entity\Ordenes;
use App\Entity\Pagos;
use App\Entity\Usuarios;
use App\Repository\CarritoComprasRepository;
use App\Repository\DireccionesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UsuariosRepository;

class OrdenesController extends AbstractController
{

    private $entityManager;
    private $session;
    private $usuariosRepository;
    private $carritoComprasRepository;
    private $direccionesRepository;

    public function __construct(EntityManagerInterface $entityManager, UsuariosRepository $usuariosRepository,CarritoComprasRepository $carritoComprasRepository,
    DireccionesRepository $direccionesRepository)
    {
        $this->entityManager = $entityManager;
        $this->usuariosRepository = $usuariosRepository;
        $this->carritoComprasRepository = $carritoComprasRepository;
        $this->direccionesRepository = $direccionesRepository;
    }


    #[Route('/ordenes', name: 'app_ordenes')]
    public function index(): Response
    {
        return $this->render('ordenes/index.html.twig', [
            'controller_name' => 'OrdenesController',
        ]);
    }


    #[Route('/crear-orden', name: 'crear_ordene')]
    public function createOrder(Request $request, SessionInterface $session)
    {
            // Simulación de entrada de PayPal con un JSON
            $paypalPaymentJson = '{
                "status": "success",
                "amount": 49.99,
                "transaction_id": "PAYPAL_TRANSACTION_ID_12345"
            }';
    
            $paypalPayment = json_decode($paypalPaymentJson);
    
            if ($paypalPayment->status === 'success') {
                // Verificar si ya existe un pago con el mismo ID de transacción
                $existingPago = $this->entityManager->getRepository(Pagos::class)->findOneBy(['transaccionId' => $paypalPayment->transaction_id]);
    
                if ($existingPago) {
                    return new Response('Este pago ya ha sido procesado.', 400);
                }
    
                // Crear el registro en la tabla pagos
                $pago = new Pagos();
                $pago->setFecha(new \DateTime());
                $pago->setMonto($paypalPayment->amount);
                $pago->setMetodoPago('PayPal');
                $pago->setTransaccionId($paypalPayment->transaction_id);
    
                $this->entityManager->persist($pago);
                $this->entityManager->flush();
    
                // Obtener el usuario actual
                $usuario = $this->usuariosRepository->findOneByEmail($session->get('user_email'));
    
                if (!$usuario) {
                    return new Response('No se encontró el usuario con el email proporcionado.', 400);
                }
    
                // Obtener las direcciones guardadas del usuario
                $direcciones = $usuario->getDirecciones();
    
                if (!$direcciones || count($direcciones) === 0) {
                    return new Response('No se encontró una dirección guardada para el usuario.', 400);
                }
    
                // Suponiendo que estamos tomando la primera dirección del conjunto
                $direccion = $direcciones[0];
    
                // Crear la orden
                $orden = new Ordenes();
                $orden->setFecha((new \DateTime())->format('Y-m-d H:i:s'));
                $orden->setTotal($paypalPayment->amount);
                $orden->setUsuarios($usuario);
                $orden->setDireccionEnvio($direccion->getCalle() . ', ' . $direccion->getCiudad() . ', ' . $direccion->getProvincia() . ', ' . $direccion->getCodigoPostal() . ', ' . $direccion->getPais());
                $orden->setPagos($pago);
                $orden->setEstado('Pendiente');
    
                $this->entityManager->persist($orden);
    
                // Obtener productos del carrito
                $carrito = $this->carritoComprasRepository->findOneBy(['usuarios' => $usuario]);
    
                if (!$carrito) {
                    return new Response('No se encontró un carrito de compras para el usuario.', 400);
                }
    
                foreach ($carrito->getProductosCarrito() as $productoCarrito) {
                    $detalleOrden = new DetallesOrden();
                    $detalleOrden->setCantidad($productoCarrito->getCantidad());
                    $detalleOrden->setPrecioUnitario($productoCarrito->getProductos()->getPrecio());
                    $detalleOrden->setOrdenes($orden);
                    $detalleOrden->setProductos($productoCarrito->getProductos());
    
                    $this->entityManager->persist($detalleOrden);
                    // Remover el producto del carrito
                    $this->entityManager->remove($productoCarrito);
                }
    
                $this->entityManager->flush();
    
                return new Response('Orden creada exitosamente y productos removidos del carrito.');
            } else {
                return new Response('Error en el pago con PayPal.', 400);
            }
}
}
 */