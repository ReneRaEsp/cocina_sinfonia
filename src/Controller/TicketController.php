<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF\TCPDF;
use Symfony\Component\HttpFoundation\Request;
use Mpdf\Mpdf;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Info;


class TicketController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/ticket", name="app_ticket")
     */
    public function index(): Response
    {
        return $this->render('ticket/index.html.twig', [
            'controller_name' => 'TicketController',
        ]);
    }

    public function generateTicket($order)
    {
        $repoInfo = $this->entityManager->getRepository(Info::class);
        $info = $repoInfo->findOneBy(['id' => 1]);
        // Configuración del ticket
        $restaurantName = $info->getName();
        $address = $info->getDir();
        $phone = $info->getTelf();
        $mail = $info->getEmail();

        // Información del pedido
        $numMesa = $order['nummesa'];
        $orderDate = $order['date'];
        $items = $order['items'];
        $totalAmount = $order['totalAmount'];

        // Generar el contenido del ticket
        $content = '';

        foreach ($items as $item) {
            $itemName = $item['name'];
            $itemQuantity = $item['cantidad'];
            $itemPrice = number_format($item['price'], 2, ',', '.');

            // Asegurar que el itemName tenga un ancho de 30 caracteres
            $itemName = str_pad($itemName, 33, ' ');


            // Asegurar que el itemPrice tenga un ancho de 10 caracteres
            $itemPrice = str_pad($itemPrice, 6, ' ', STR_PAD_LEFT);

            $bad_content = "
            $itemQuantity      $itemName$itemPrice\n";


            $content .= "\n" . trim($bad_content);
        }

        // $content .=  "\n\n                            Total: " . number_format($totalAmount, 2, ',', '.') . "€";





        return $content;
    }


    public function generateTicketKitchen($order)
    {
 // Información del pedido
 $numMesa = $order['nummesa'];
 $items = $order['items'];

 // Inicializar secciones
 $sections = [
     '1º' => [],
     '2º' => [],
     '3º' => [],
     'postre' => []
 ];

 // Clasificar los ítems en las secciones correspondientes
 foreach ($items as $item) {
     $numPlato = $item['numplato'];
     switch ($numPlato) {
         case 1:
             $sections['1º'][] = $item;
             break;
         case 2:
             $sections['2º'][] = $item;
             break;
         case 3:
             $sections['3º'][] = $item;
             break;
         case 4:
             $sections['postre'][] = $item;
             break;
         default:
             // Si numplato no es 1, 2, 3 o 4, puedes decidir cómo manejarlo aquí
             break;
     }
 }

 // Generar el contenido del ticket
 $content = "";

 foreach ($sections as $sectionName => $items) {
     if (count($items) > 0) {
         // Agregar el título de la sección
         $content .= strtoupper(str_replace('_', ' ', $sectionName)) . ":\n";

         foreach ($items as $item) {
             // Obtiene el nombre del ítem y lo ajusta para tener un ancho de 10 caracteres
             $itemName = trim(str_pad($item['name'], 10, ' '));

             // Obtiene la cantidad del ítem
             $itemQuantity = $item['cantidad'];

             // Obtiene el comentario del ítem
             $comentario = $item['comentario'];

             // Verifica si hay un comentario
             $comentarioTexto = ($comentario !== '') ? " ($comentario)" : '';

             // Obtiene los extras del ítem
             $extras = $item['extras'];

             // Extrae los nombres de los extras
             $nombres = array_column($extras, 'nombre');

             // Convierte los nombres en una cadena de texto separada por comas
             $cadenaExtras = implode(', ', $nombres);

             // Construye la cadena de texto
             $bad_content = "$itemQuantity $itemName$comentarioTexto";

             // Agrega la parte de los extras solo si hay extras
             if (!empty($cadenaExtras)) {
                 $bad_content .= " - Extras: $cadenaExtras";
             }

             // Agrega el contenido del ítem al ticket
             $content .= "\n" . trim($bad_content) . "\n\n";
         }

         // Agregar un salto de línea adicional después de cada sección
         $content .= "\n\n";
     }
 }

 // Finalmente, elimina cualquier espacio en blanco adicional alrededor del contenido
 return trim($content);
    }
    public function generateTicketBarra($order)
    {


        // Información del pedido
        $numMesa = $order['nummesa'];
        $items = $order['items'];


        // Generar el contenido del ticket
        $content = "";

        foreach ($items as $item) {
            // Obtiene el nombre del ítem y lo ajusta para tener un ancho de 10 caracteres
            $itemName = trim(str_pad($item['name'], 10, ' '));

            // Obtiene la cantidad del ítem
            $itemQuantity = $item['cantidad'];
            
            // // Obtiene el comentario del ítem
            // $comentario = $item['comentario'];
            
            // // Verifica si hay un comentario
            // $comentarioTexto = ($comentario !== '') ? "($comentario)" : '';
            
            // // Obtiene los extras del ítem
            // $extras = $item['extras'];
            
            // Extrae los nombres de los extras
            // $nombres = array_column($extras, 'nombre');
            
            // Convierte los nombres en una cadena de texto separada por comas
            // $cadenaExtras = implode(', ', $nombres);
            
            // Construye la cadena de texto
            $bad_content = "$itemQuantity $itemName";
            
            // Agrega la parte de los extras solo si hay extras
            if (!empty($cadenaExtras)) {
                $bad_content .= " - Extras: $cadenaExtras";
            }
            
            // Agrega el contenido del ítem al ticket
            $content .= "\n\n" . trim($bad_content);
            
            // Agrega un salto adicional después de cada línea
            $content .= "\n\n";

            // Puedes agregar más saltos adicionales si lo necesitas
            $content .= "\n\n";
            
            // Agrega más contenido al ticket si es necesario
            
            // Finalmente, elimina cualquier espacio en blanco adicional alrededor del contenido
            $content = trim($content);
        }



        return $content;
    }

    public function generateTicketTienda($order)
    {
        $repoInfo = $this->entityManager->getRepository(Info::class);
        $info = $repoInfo->findOneBy(['id' => 1]);
        // Configuración del ticket
        $restaurantName = $info->getName();
        $address = $info->getDir();
        $phone = $info->getTelf();
        $mail = $info->getEmail();

        // Información del pedido
        $items = $order['items'];
        $totalAmount = $order['totalAmount'];

        // Generar el contenido del ticket
        $content = '';

        foreach ($items as $item) {
            $itemName = $item['name'];
            $itemQuantity = $item['cantidad'];
            $itemPrice = number_format($item['price'], 2, ',', '.');

            // Asegurar que el itemName tenga un ancho de 30 caracteres
            $itemName = str_pad($itemName, 33, ' ');


            // Asegurar que el itemPrice tenga un ancho de 10 caracteres
            $itemPrice = str_pad($itemPrice, 6, ' ', STR_PAD_LEFT);

            $bad_content = "
            $itemQuantity      $itemName$itemPrice\n";


            $content .= "\n" . trim($bad_content);
        }

        // $content .=  "\n\n                            Total: " . number_format($totalAmount, 2, ',', '.') . "€";





        return $content;
    }
}
