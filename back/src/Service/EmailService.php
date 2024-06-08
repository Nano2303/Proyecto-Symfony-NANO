<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Symfony\Component\HttpFoundation\Request;

class EmailService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function enviarDetallesDeOrden($data)
    {
   
    $fecha = $data['fecha'];
    $usuario = $data['usuario'];
    $direccionEnvio = $data['direccion_envio'];
    $pago = $data['pago'];
    $productos = $data['productos'];
    $estado = $data['estado'];
    $total = $data['total'];

    // Construir el cuerpo del correo electrónico
    $body = "
        <h1>Detalles de la Orden</h1>
        <p><strong>Fecha:</strong> $fecha</p>
        <h2>Información del Usuario</h2>
        <p><strong>Nombre:</strong> {$usuario['nombre']}</p>
        <p><strong>Email:</strong> {$usuario['email']}</p>
        <h2>Dirección de Envío</h2>
        <p><strong>Calle:</strong> {$direccionEnvio['calle']}</p>
        <p><strong>Ciudad:</strong> {$direccionEnvio['ciudad']}</p>
        <p><strong>Provincia:</strong> {$direccionEnvio['provincia']}</p>
        <p><strong>Código Postal:</strong> {$direccionEnvio['codigo_postal']}</p>
        <p><strong>País:</strong> {$direccionEnvio['pais']}</p>
        <h2>Detalles del Pago</h2>
        <p><strong>Monto:</strong> {$pago['monto']}</p>
        <p><strong>Método:</strong> {$pago['metodo']}</p>
        <h2>Productos</h2>";

    foreach ($productos as $producto) {
        $body .= "
            <p><strong>Nombre:</strong> {$producto['nombre']}</p>
            <p><strong>Cantidad:</strong> {$producto['cantidad']}</p>
            <p><strong>Precio Unitario:</strong> {$producto['precio_unitario']}</p>
            <p><strong>Subtotal:</strong> {$producto['subtotal']}</p>";
    }

    $body .= "
        <h2>Estado de la Orden</h2>
        <p><strong>Estado:</strong> $estado</p>
        <h2>Total</h2>
        <p><strong>Total:</strong> $total</p>";


    $this->enviarEmail($usuario['email'],'Detalles de tu compra', $body);
    }



    public function generarCodigoRecuperacion(): int
    {
        return mt_rand(100000, 999999);
    }



    public function enviarEmail($to,$subject,$body){

        $mail = new PHPMailer(true);

        try {
            // Configurar el servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Cambiar a tu servidor SMTP
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ptachavikerico@gmail.com'; // Cambiar a tu dirección de correo electrónico
            $mail->Password   = 'uldzuwvqfpqhmhju'; // Cambiar a tu contraseña de correo electrónico
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O 'tls' si tu servidor lo requiere
            $mail->Port       = 587; // O el puerto SMTP de tu servidor
    
            // Configurar remitente y destinatario
            $mail->setFrom('ptachavikerico@gmail.com', 'Synonym');
            $mail->addAddress($to); // Agregar el destinatario
    
            // Contenido del correo electrónico
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
    
            // Enviar el correo electrónico
            $mail->send();
        } catch (Exception $e) {
        
        }


    }



    public function enviarCodigoRecuperarContrasena($usuario_email): int
    {

        $codigo_recuperacion =$this->generarCodigoRecuperacion();
        $body ='Aqui va tu codigo de recuperacion: '.$codigo_recuperacion;
        $this->enviarEmail($usuario_email,'Codigo recuperacion de contraseña', $body);

        return $codigo_recuperacion;
    
    }

    
}
