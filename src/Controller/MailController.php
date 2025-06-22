<?php
namespace App\Controller;

use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    private $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/send-mail", name="send_mail")
     */
    public function sendMail($email, $pdf): Response
    {
        $to = $email;
        $subject = 'Factura de su ticket';
       $pdfPath = $pdf; // Ruta absoluta al archivo PDF

        try {
            $this->mailerService->sendEmailWithAttachment($to, $subject, $pdfPath);
            return new Response('Email sent successfully', Response::HTTP_OK);
        } catch (\Exception $e) {
            // Manejo de errores (por ejemplo, loguear el error)
            return new Response('Failed to send email: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
