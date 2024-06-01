<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function enviarDetallesDeOrden($user, $order)
    {
        $email = (new Email())
            ->from('your@email.com')
            ->to($user->getEmail())
            ->subject('Detalles de la orden')
            ->html(
                '<p>Aquí están los detalles de tu orden:</p>' .
                    '<p>Número de orden: ' . $order->getOrderNumber() . '</p>'
            );

        $this->mailer->send($email);
    }

    function generarCodigoRecuperacion(): int
    {
        return mt_rand(100000, 999999);
    }

    public function enviarCodigoRecuperarContrasena($usuario_email): bool
    {
        var_dump($usuario_email);

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
            $mail->addAddress($usuario_email); // Agregar el destinatario
    
            // Contenido del correo electrónico
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña';
            $mail->Body    = 'Aquí va el codigo de recuperacion de contraseña: '.$this->generarCodigoRecuperacion();
    
            // Enviar el correo electrónico
            $mail->send();
            return true;
        } catch (Exception $e) {
            // Si hay algún error, puedes manejarlo aquí
            echo "Error al enviar el correo electrónico: {$mail->ErrorInfo}";
            return false;
        }
        
    }
}
