<?php

namespace App\Service;

use App\Entity\Info;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Doctrine\ORM\EntityManagerInterface;

class MailerService
{
    private $mailer;
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = new PHPMailer(true);
        $this->entityManager = $entityManager;
    }

    public function sendEmailWithAttachment(string $to, string $subject, string $pdfPath)
    {
        $info = $this->entityManager->getRepository(Info::class)->findOneBy(['id' => 1]);
        $infoName = mb_convert_encoding($info->getName(), 'UTF-8', 'auto');
        $infoEmail = mb_convert_encoding($info->getEmail(), 'UTF-8', 'auto');
        $infoTelf = mb_convert_encoding($info->getTelf(), 'UTF-8', 'auto');


        try {
            // Configuración del servidor SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = 'bold-poitras.217-160-115-204.plesk.page';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'simoft@w3bcn.es';
            $this->mailer->Password = 'W3BarcelonaIvanyJaume';
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->SMTPSecure = 'tls'; // o 'ssl'
            $this->mailer->Port = 587;


            // Remitente y destinatarios
            $this->mailer->setFrom('simoft@w3bcn.es', $infoName);
            $this->mailer->addAddress($to);

            // Contenido del correo
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8'; // Asegurar la codificación UTF-8
            $this->mailer->Subject = $subject . ' ' . $infoName;
            $this->mailer->Body = '
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 20px;
                                background-color: #f4f4f4;
                            }
                            .container {
                                background-color: #fff;
                                padding: 20px;
                                border-radius: 5px;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                            }
                            .header {
                                background-color: #333;
                                color: #fff;
                                padding: 10px;
                                text-align: center;
                                border-radius: 5px 5px 0 0;
                            }
                            .content {
                                margin-top: 20px;
                            }
                            .content p {
                                margin: 0 0 10px;
                            }
                            .footer {
                                margin-top: 20px;
                                text-align: center;
                                font-size: 12px;
                                color: #777;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            <div class="header">
                                <h1>' . htmlspecialchars('Factura de su Pedido', ENT_QUOTES, 'UTF-8') . '</h1>
                            </div>
                            <div class="content">
                                <p>' . htmlspecialchars('Estimado/a Cliente,', ENT_QUOTES, 'UTF-8') . '</p>
                                <p>' . htmlspecialchars('Gracias por su pedido. Adjunto encontrará la factura de su pedido.', ENT_QUOTES, 'UTF-8') . '</p>
                                <p>' . htmlspecialchars('Si tiene alguna pregunta o necesita asistencia adicional, no dude en contactarnos.', ENT_QUOTES, 'UTF-8') . ' En nuestro correo electrónico ' . htmlspecialchars($infoEmail, ENT_QUOTES, 'UTF-8') . ' o teléfono ' . htmlspecialchars($infoTelf, ENT_QUOTES, 'UTF-8') . '</p>
                                <p>' . htmlspecialchars('Esperamos verle de nuevo pronto.', ENT_QUOTES, 'UTF-8') . '</p>
                                <p>' . htmlspecialchars('Atentamente,', ENT_QUOTES, 'UTF-8') . '</p>
                                <p>' . htmlspecialchars('El equipo de ' . $infoName, ENT_QUOTES, 'UTF-8') . '</p><br>
                                <p><strong>' . htmlspecialchars('Esto es un mensaje autómatico desde ', ENT_QUOTES, 'UTF-8') . '<a href="simoft.es">' . htmlspecialchars('Simoft', ENT_QUOTES, 'UTF-8') . '</a>, ' . htmlspecialchars('no responder a este correo.', ENT_QUOTES, 'UTF-8') . '</strong></p>
                            </div>
                            <div class="footer">
                                <p>&copy; 2024 ' . htmlspecialchars($infoName, ENT_QUOTES, 'UTF-8') . '. ' . htmlspecialchars('Todos los derechos reservados.', ENT_QUOTES, 'UTF-8') . '</p>
                            </div>
                        </div>
                    </body>
                    </html>';

            $this->mailer->AltBody = strip_tags('esto es una prueba');


            // Adjuntar archivo si se proporciona la ruta
            if ($pdfPath) {
                $this->mailer->addAttachment($pdfPath);
            }

            $this->mailer->send();
        } catch (Exception $e) {
            throw new \Exception("Error al enviar el correo: {$this->mailer->ErrorInfo}");
        }
    }
}
