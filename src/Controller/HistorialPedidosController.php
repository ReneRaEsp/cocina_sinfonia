<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\HistorialPedidos;
use App\Entity\Tickettofactura;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;


class HistorialPedidosController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/historial/pedidos", name="app_historial_pedidos")
     */
    public function index(): Response
    {
        return $this->render('historial_pedidos/index.html.twig', [
            'controller_name' => 'HistorialPedidosController',
        ]);
    }

    /**
     * @Route("/listrefs", name="list_refs")
     */
    public function listRefs(Request $request)
    {
        $pedidos = $request->query->get('pedidos');

        $pedidos = explode(",", $pedidos);

        $em = $this->getDoctrine()->getManager();

        $historialPedidosData = [];

        $pedidos = $em->getRepository(HistorialPedidos::class)->findBy(['num_ref' => $pedidos]);

        $currentYear = (new DateTime())->format('Y'); // Obtener el año actual

        $ticketFechas = $this->entityManager->getRepository(Tickettofactura::class)->findInvoiceDate();

        // Verifica si el array no está vacío
        if (!empty($ticketFechas)) {
            // Obtén el último elemento del array
            $ultimoTicket = end($ticketFechas);

            if (count($ticketFechas) <= 9) {

                $num = sprintf('%02d', count($ticketFechas)+1);
            } else {

                $num = (count($ticketFechas)+1);
            }


            // Extrae el año de la propiedad 'fecha'
            $dateAño = new DateTime($ultimoTicket['fecha']);

            $año = $dateAño->format('Y');

            $idfactura = $año . $num;
        } else {
            $idfactura = $currentYear . '01';
        }




        foreach ($pedidos as $item) {
            $temp = array(
                'id' => $item->getId(),
                'mesa' => $item->getMesa(),
                'comida' => $item->getComida(),
                'comensales' => $item->getComensales(),
                'user' => $item->getUser(),
                'precio' => $item->getPrecio(),
                'precio_total' => $item->getPrecioTotal(),
                'num_ref' => $item->getNumRef(),
                'fecha' => $item->getFecha(),
                'iva' => $item->getIva(),
                'idfactura' => $idfactura,
            );
            array_push($historialPedidosData, $temp);
        }


        return new JsonResponse([
            'historial' => $historialPedidosData
        ]);
    }
}
