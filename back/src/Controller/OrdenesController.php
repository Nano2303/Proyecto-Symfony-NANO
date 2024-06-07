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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function __construct(
        EntityManagerInterface $entityManager,
        UsuariosRepository $usuariosRepository,
        CarritoComprasRepository $carritoComprasRepository,
        DireccionesRepository $direccionesRepository
    ) {
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
            "amount": 49.99
        }';

        $paypalPayment = json_decode($paypalPaymentJson);

        if ($paypalPayment->status === 'success') {
            // Crear el registro en la tabla pagos
            $pago = new Pagos();
            $pago->setFecha(new \DateTime());
            $pago->setMonto($paypalPayment->amount);
            $pago->setMetodoPago('PayPal');

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
            $orden->setUsuarios($usuario);
            $orden->setDireccionEnvio($direccion->getCalle() . ', ' . $direccion->getCiudad() . ', ' . $direccion->getProvincia() . ', ' . $direccion->getCodigoPostal() . ', ' . $direccion->getPais());
            $orden->setPagos($pago);
            $orden->setEstado('Pendiente');

            // Inicializar el total de la orden
            $totalOrden = 0;

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

                // Calcular el total de la orden
                $totalOrden += $productoCarrito->getCantidad() * $productoCarrito->getProductos()->getPrecio();

                // Remover el producto del carrito
                $this->entityManager->remove($productoCarrito);
            }

            // Establecer el total de la orden
            $orden->setTotal($totalOrden);

            // Ajustar el total del carrito de compras a cero
            $carrito->setTotal(0);

            $this->entityManager->persist($orden);
            $this->entityManager->persist($carrito);
            $this->entityManager->flush();

            return new Response('Orden creada exitosamente y productos removidos del carrito.');
        } else {
            return new Response('Error en el pago con PayPal.', 400);
        }
    }



   #[Route('/obtener-ordenes', name: 'obtener_ordenes', methods: ['GET'])]
    public function getOrders(): Response
    {
        // Obtener todas las órdenes desde la base de datos
        $ordenes = $this->entityManager->getRepository(Ordenes::class)->findAll();

        // Transformar las órdenes a un array para devolver como JSON
        $ordenesData = [];
        foreach ($ordenes as $orden) {
            $ordenesData[] = $this->getDetallesOrden($orden);
        }

        return $this->json($ordenesData);
    }

    private function getDetallesOrden(Ordenes $orden): array
    {
        $detallesOrden = [];

        foreach ($orden->getDetallesOrden() as $detalle) {
            $producto = $detalle->getProductos();
            $detallesOrden[] = [
                'producto_id' => $producto->getId(),
                'producto_nombre' => $producto->getNombre(),
                'producto_descripcion' => $producto->getDescripcion(),
                'producto_precio' => $producto->getPrecio(),
                'cantidad' => $detalle->getCantidad(),
                'precioUnitario' => $detalle->getPrecioUnitario(),
                'total' => $detalle->getCantidad() * $detalle->getPrecioUnitario(),
            ];
        }

        $pago = $orden->getPagos();

        return [
            'id' => $orden->getId(),
            'fecha' => $orden->getFecha(),
            'total' => $orden->getTotal(),
            'usuario' => $orden->getUsuarios()->getEmail(),
            'direccionEnvio' => $orden->getDireccionEnvio(),
            'estado' => $orden->getEstado(),
            'pago' => [
                'monto' => $pago->getMonto(),
                'metodo' => $pago->getMetodoPago(),
                'fecha' => $pago->getFecha()->format('Y-m-d H:i:s'),
            ],
            'detalles' => $detallesOrden,
        ];
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