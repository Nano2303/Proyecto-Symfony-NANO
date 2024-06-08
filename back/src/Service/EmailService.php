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
   
     // Extraer detalles de la orden
     $fecha = $data['fecha'];
     $usuario = $data['usuario'];
     $direccionEnvio = $data['direccion_envio'];
     $pago = $data['pago'];
     $productos = $data['productos'];
     $estado = $data['estado'];
     $total = $data['total'];
 
     // Construir el cuerpo del correo electrónico en formato de ticket
     $body = "
         <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; background: #f9f9f9;'>
             <h1 style='text-align: center; font-size: 24px; margin-bottom: 20px;'>SYNONYM</h1>
             <h2 style='text-align: center; font-size: 16px; margin-bottom: 10px;'>Detalles de la Orden</h2>
             <p style='text-align: center; font-size: 12px; margin-bottom: 10px;'>Fecha: <strong>$fecha</strong></p>
             <hr style='border: none; border-top: 1px solid #ccc;'>
             <table style='width: 100%; font-size: 12px; border-collapse: collapse;'>
                 <tr>
                     <th style='text-align: left; padding: 8px; background-color: #f2f2f2;'>Información del Usuario</th>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Nombre: {$usuario['nombre']}</td>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Email: {$usuario['email']}</td>
                 </tr>
                 <tr>
                     <th style='text-align: left; padding: 8px; background-color: #f2f2f2;'>Dirección de Envío</th>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Calle: {$direccionEnvio['calle']}</td>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Ciudad: {$direccionEnvio['ciudad']}</td>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Provincia: {$direccionEnvio['provincia']}</td>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Código Postal: {$direccionEnvio['codigo_postal']}</td>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>País: {$direccionEnvio['pais']}</td>
                 </tr>
                 <tr>
                     <th style='text-align: left; padding: 8px; background-color: #f2f2f2;'>Detalles del Pago</th>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Monto: {$pago['monto']}</td>
                 </tr>
                 <tr>
                     <td style='padding: 8px;'>Método: {$pago['metodo']}</td>
                 </tr>
             </table>
             <hr style='border: none; border-top: 1px solid #ccc; margin: 20px 0;'>
             <h2 style='font-size: 14px; margin-bottom: 10px;'>Productos</h2>
             <table style='width: 100%; font-size: 12px; border-collapse: collapse;'>
                 <thead>
                     <tr>
                         <th style='border-bottom: 1px solid #ddd; text-align: left;'>Cantidad</th>
                         <th style='border-bottom: 1px solid #ddd; text-align: left;'>Producto</th>
                         <th style='border-bottom: 1px solid #ddd; text-align: right;'>Subtotal</th>
                     </tr>
                 </thead>
                 <tbody>";
     
     if (is_array($productos) && !empty($productos)) {
         $count = 0;
         foreach ($productos as $producto) {
             $body .= "
                 <tr>
                     <td style='border-bottom: 1px solid #ddd;'>{$producto['cantidad']}</td>
                     <td style='border-bottom: 1px solid #ddd;'>{$producto['nombre']}</td>
                     <td style='border-bottom: 1px solid #ddd; text-align: right;'>{$producto['precio_unitario']} × {$producto['cantidad']} = {$producto['subtotal']}</td>
                 </tr>";
             $count++;
         }
     } else {
         $body .= "
             <tr>
                 <td colspan='3' style='text-align: center;'>No hay productos en la orden.</td>
             </tr>";
     }
 
     $body .= "
                 </tbody>
             </table>
             <h2 style='font-size: 14px; margin: 20px 0 10px;'>Estado de la Orden</h2>
             <p style='font-size: 12px;'><strong>Estado:</strong> $estado</p>
             <h2 style='font-size: 14px; margin: 20px 0 10px;'>Total</h2>
             <p style='font-size: 12px;'><strong>Total:</strong> $total</p>
             <hr style='border: none; border-top: 1px solid #ccc;'>
             <p style='font-size: 12px; text-align: center;'>Gracias por su compra!</p>
         </div>";
 
     // Envía el correo
     $this->enviarEmail($usuario['email'], 'Detalles de tu compra', $body);
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
