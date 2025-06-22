<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Info;
use App\Entity\Tickets;
use App\Entity\SunmiCloudPrinter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TicketCobroController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    /**
     * @Route("/ticket/cobro", name="app_ticket_cobro")
     */
    public function index(): Response
    {
        return $this->render('ticket_cobro/index.html.twig', [
            'controller_name' => 'TicketCobroController',
        ]);
    }

    public function printTicket(Request $request): JsonResponse
    {
        // Recuperar el contenido del ticket de la solicitud
        $contentWithVariables = $request->request->get('json');

        // Separar el contenido del ticket y las variables
        $ticketData = explode('|', $contentWithVariables);
        $content = $ticketData[0];
        $num_mesa = $ticketData[1];
        $a_pagar = $ticketData[2];
        $comensales = $ticketData[3];

        // Aquí puedes realizar la lógica de impresión utilizando el contenido del ticket y las variables

        // Por ejemplo, podrías llamar a una función que maneje la impresión del ticket
        $this->printTicketContent($content, $num_mesa, $a_pagar, $comensales);

        // Devolver una respuesta JSON
        return new JsonResponse(['status' => 'success']);
    }

    private function printTicketContent(string $content, string $num_mesa, float $a_pagar, int $comensales): void
    {
        // Aquí implementa la lógica para imprimir el contenido del ticket
        // Puedes usar la clase SunmiCloudPrinter u otra biblioteca para interactuar con la impresora
        // Ejemplo:

       
    }
}
