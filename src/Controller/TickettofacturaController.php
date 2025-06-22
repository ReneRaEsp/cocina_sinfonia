<?php

namespace App\Controller;

use App\Entity\Tickettofactura;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TickettofacturaRepository;
use App\Controller\MailController;
use App\Entity\Ventas;
use DateTime;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Mailer\MailerInterface;

class TickettofacturaController extends AbstractController
{

    private $entityManager;
    private $security;
    private $ticketFacturaRepository;
    private $mailController;

    public function __construct(EntityManagerInterface $entityManager, Security $security, TickettofacturaRepository $ticketFacturaRepository, MailController $mailController)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->ticketFacturaRepository = $ticketFacturaRepository;
        $this->mailController = $mailController;
    }
    /**
     * @Route("/tickettofactura", name="tickettofactura")
     */
    public function index(): Response
    {

        $tickettofactura = $this->ticketFacturaRepository->findAll();

        $data_tf = array();

        foreach ($tickettofactura as $tf) {

            $temp = array(
                'id' => $tf->getId(),
                'ticketref' => $tf->getRef(),
                'fecha' => $tf->getFecha()->format('d/m/Y'),
                'ruta' => $tf->getRuta(),

            );

            array_push($data_tf, $temp);


        };

        $ventas = $this->entityManager->getRepository(Ventas::class)->findAll();

        // Obtener la fecha actual
        $fecha = new DateTime();

        // Definir los tramos
        $tramos = [
            ['inicio' => '01-01', 'fin' => '03-31', 'tramo' => 'Primer Tramo (Enero - Marzo)'],
            ['inicio' => '04-01', 'fin' => '06-30', 'tramo' => 'Segundo Tramo (Abril - Junio)'],
            ['inicio' => '07-01', 'fin' => '09-30', 'tramo' => 'Tercer Tramo (Julio - Septiembre)'],
            ['inicio' => '10-01', 'fin' => '12-31', 'tramo' => 'Cuarto Tramo (Octubre - Diciembre)']
        ];

        $inicioTramo = 0;
        $finTramo = 0;
        $tramo = '';

        // Iterar a través de los tramos y verificar si la fecha actual está en uno de ellos
        foreach ($tramos as $tramo) {
            $inicio = new DateTime($fecha->format('Y') . '-' . $tramo['inicio']);
            $fin = new DateTime($fecha->format('Y') . '-' . $tramo['fin']);

            // Verificar si la fecha actual está en el tramo
            if ($fecha >= $inicio && $fecha <= $fin) {

                $inicioTramo = $inicio;
                $finTramo = $fin;
                $inicioTramoString = $tramo['inicio'];
                $finTramoString = $tramo['fin'];
                $tramo = $tramo['tramo'];
                break;
            }
        }

        $array_years = array();

        $totalImpuesto = 0;
        $iva0 = 0;
        $iva10 = 0;
        $iva5 = 0;
        $iva21 = 0;
        foreach ($ventas as $venta) {
            $año = $venta->getFecha()->format('Y');

            if (!in_array($año, $array_years)) {
                $array_years[] = $año;
            }
            if ($venta->getFecha() >= $inicioTramo && $venta->getFecha() <= $finTramo) {
                    if ($venta->getIva() === 0) {

                        $iva0 += $venta->getImporteIva();
                        $totalImpuesto += $venta->getImporteIva();
                    } else if ($venta->getIva() === 5) {

                        $iva5 += $venta->getImporteIva();
                        $totalImpuesto += $venta->getImporteIva();
                    } else if ($venta->getIva() === 10) {

                        $iva10 += $venta->getImporteIva();
                        $totalImpuesto += $venta->getImporteIva();
                    } else if ($venta->getIva() === 21) {

                        $iva21 += $venta->getImporteIva();
                        $totalImpuesto += $venta->getImporteIva();
                    }
                
            }
        }


        return $this->render('tickettofactura/index.html.twig', [
            'controller_name' => 'TickettofacturaController',
            'tickettofactura' => $data_tf,
            'tramo' => $tramo,
                'fechaInicio' => $inicioTramo,
                'fechaFin' => $finTramo,
                'años' => $array_years,
                'iva0' => number_format($iva0, 2, ',', '.'),
                'iva5' => number_format($iva5, 2, ',', '.'),
                'iva10' => number_format($iva10, 2, ',', '.'),
                'iva21' => number_format($iva21, 2, ',', '.'),
                'totalimpuestos' => number_format($totalImpuesto, 2, ',', '.'),
        ]);
    }


    /**
     * @Route("/guardarpdf", name="guardar_pdf" , methods={"POST"})
     */
    public function guardarPdf(Request $request): Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $num_ref = $request->request->get('num_ref');
        $fecha = $request->request->get('fecha');
        $email = $request->request->get('email');

        $currentYear = (new DateTime())->format('Y'); // Obtener el año actual

        $ticketFechas = $this->entityManager->getRepository(Tickettofactura::class)->findInvoiceDate();

        // Verifica si el array no está vacío
        if (!empty($ticketFechas)) {
            // Obtén el último elemento del array
            $ultimoTicket = end($ticketFechas);

            if (count($ticketFechas) <= 9) {

                $num = sprintf('%02d', count($ticketFechas)+1);
            } else {

                $num = count($ticketFechas)+1;
            }


            // Extrae el año de la propiedad 'fecha'
            $dateAño = new DateTime($ultimoTicket['fecha']);

            $año = $dateAño->format('Y');

            $idfactura = $año . $num;
        } else {
            $idfactura = $currentYear . '01';
        }

        if (!$file) {
            return new JsonResponse(['error' => 'No se ha enviado ningún archivo.'], Response::HTTP_BAD_REQUEST);
        }

        $uploadsDirectory = $this->getParameter('uploads_directory');
        $filesystem = new Filesystem();

        // Verificar si la carpeta de subidas existe y crearla si no es así
        if (!$filesystem->exists($uploadsDirectory)) {
            try {
                $filesystem->mkdir($uploadsDirectory, 0755);
            } catch (IOExceptionInterface $exception) {
                return new JsonResponse(['error' => 'Error al crear la carpeta de subidas.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
        $newFilename = 'Factura_'.$idfactura.'.'.$file->guessExtension();


        $ticketRepetido = $this->entityManager->getRepository(Tickettofactura::class)->findOneBy(['ref' => $num_ref]);

        if($ticketRepetido){
            return new JsonResponse(['error' => 'Esta factura ya ha sido creada.']);

        }

        try {
            $file->move($uploadsDirectory, $newFilename);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Error al subir el archivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $absoluteFilePath = realpath($uploadsDirectory . '/' . $newFilename);

        // Aquí puedes guardar la ruta en la base de datos
        $fileEntity = new Tickettofactura();
        $fileEntity->setRuta('/ticketfacturasPdf/'.$newFilename);
        $fileEntity->setRef($num_ref);
        $fileEntity->setFecha(new \DateTime($fecha));
        $this->entityManager->persist($fileEntity);
        $this->entityManager->flush();

        try {
            // Llamar al método sendMail
            $response = $this->mailController->sendMail($email, $absoluteFilePath);
    
            // Puedes verificar el contenido de la respuesta
            if ($response->getStatusCode() === Response::HTTP_OK) {
                return new JsonResponse(['success' =>'Correo enviado exitosamente'], Response::HTTP_OK);
            } else {
                return new JsonResponse(['error' => 'Error al enviar el correo: ' . $response->getContent()], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            // Manejo de errores en caso de que sendMail lance una excepción
            return new JsonResponse(['error' => 'Error inesperado: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // return new JsonResponse(['message' => 'Archivo subido con éxito', 'filePath' => $newFilename]);
    }

    /**
     * @Route("/buscartramoemitida", name="buscar_tramo_emitida")
     */
    public function buscarTramo(Request $request): Response
    {

        $numTramo = intval($request->request->get('tramo'));
        $año = intval($request->request->get('año'));


        $repoVentas = $this->entityManager->getRepository(Ventas::class);
        $ventas = $repoVentas->findAll();


        // Definir los tramos
        $tramos = [
            ['inicio' => '01-01', 'fin' => '03-31', 'tramo' => 'Primer Tramo (Enero - Marzo)'],
            ['inicio' => '04-01', 'fin' => '06-30', 'tramo' => 'Segundo Tramo (Abril - Junio)'],
            ['inicio' => '07-01', 'fin' => '09-30', 'tramo' => 'Tercer Tramo (Julio - Septiembre)'],
            ['inicio' => '10-01', 'fin' => '12-31', 'tramo' => 'Cuarto Tramo (Octubre - Diciembre)']
        ];

        $inicioTramo = 0;
        $finTramo = 0;



        $inicio = new DateTime($año . '-' . $tramos[$numTramo]['inicio']);
        $fin = new DateTime($año . '-' . $tramos[$numTramo]['fin']);
        $tramo = $tramos[$numTramo]['tramo'];


        $inicioTramo = $inicio;
        $finTramo = $fin;


            $totalImpuesto = 0;
            $iva0 = 0;
            $iva10 = 0;
            $iva5 = 0;
            $iva21 = 0;
            foreach ($ventas as $venta) {
                if ($venta->getFecha() >= $inicioTramo && $venta->getFecha() <= $finTramo) {
                        if ($venta->getIva() === 0) {
    
                            $iva0 += $venta->getImporteIva();
                            $totalImpuesto += $venta->getImporteIva();
                        } else if ($venta->getIva() === 5) {
    
                            $iva5 += $venta->getImporteIva();
                            $totalImpuesto += $venta->getImporteIva();
                        } else if ($venta->getIva() === 10) {
    
                            $iva10 += $venta->getImporteIva();
                            $totalImpuesto += $venta->getImporteIva();
                        } else if ($venta->getIva() === 21) {
    
                            $iva21 += $venta->getImporteIva();
                            $totalImpuesto += $venta->getImporteIva();
                        }
                    
                }
            }


        return new JsonResponse([
            'tramo' => $tramo,
            'iva0' => $iva0,
            'iva5' => $iva5,
            'iva10' => $iva10,
            'iva21' => $iva21,
            'totalimpuestos' => $totalImpuesto
        ]);
    }

     /**
     * @Route("/fechatramoemitida", name="fecha_tramo_emitida")
     */
    public function fechaTramo(Request $request): Response
    {

        $fecha = $request->request->get('fecha');


        $repoVentas = $this->entityManager->getRepository(Ventas::class);
        $ventas = $repoVentas->findAll();


        // Definir los tramos
        $tramos = [
            ['inicio' => '01-01', 'fin' => '03-31', 'tramo' => 'Primer Tramo (Enero - Marzo)'],
            ['inicio' => '04-01', 'fin' => '06-30', 'tramo' => 'Segundo Tramo (Abril - Junio)'],
            ['inicio' => '07-01', 'fin' => '09-30', 'tramo' => 'Tercer Tramo (Julio - Septiembre)'],
            ['inicio' => '10-01', 'fin' => '12-31', 'tramo' => 'Cuarto Tramo (Octubre - Diciembre)']
        ];



        $fecha = new DateTime($fecha);

        // var_dump($fecha->format('d-m-Y'));


        $totalImpuesto = 0;
        $iva0 = 0;
        $iva10 = 0;
        $iva5 = 0;
        $iva21 = 0;
        foreach ($ventas as $venta) {
            if ($venta->getFecha()->format('d-m-Y') === $fecha->format('d-m-Y')) {
                if ($venta->getIva() === 0) {
    
                    $iva0 += $venta->getImporteIva();
                    $totalImpuesto += $venta->getImporteIva();
                } else if ($venta->getIva() === 5) {

                    $iva5 += $venta->getImporteIva();
                    $totalImpuesto += $venta->getImporteIva();
                } else if ($venta->getIva() === 10) {

                    $iva10 += $venta->getImporteIva();
                    $totalImpuesto += $venta->getImporteIva();
                } else if ($venta->getIva() === 21) {

                    $iva21 += $venta->getImporteIva();
                    $totalImpuesto += $venta->getImporteIva();
                }
            }
        }





        return new JsonResponse([
            'fecha' => $fecha->format('d-m-Y'),
            'iva0' => $iva0,
            'iva5' => $iva5,
            'iva10' => $iva10,
            'iva21' => $iva21,
            'totalimpuestos' => $totalImpuesto
        ]);
    }
}
