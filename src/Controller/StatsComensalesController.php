<?php

namespace App\Controller;

use App\Entity\Comida;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Ventas;
use App\Entity\Zonas;
use App\Repository\StatscomensalesRepository;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class StatsComensalesController extends AbstractController
{

    private $entityManager;
    private $statsRepository;

    public function __construct(EntityManagerInterface $entityManager, StatscomensalesRepository $statsRepository)
    {
        $this->entityManager = $entityManager;
        $this->statsRepository = $statsRepository;
    }
    /**
     * @Route("/statscomensales", name="statscomensales")
     */
    public function index(): Response
    {


        //MEDIA DE COMENSALES POR FRANJAS HORARIAS
        $zonas = $this->entityManager->getRepository(Zonas::class)->findAll();

        $fechaActual = new DateTime();

        // Restar un día
        $fechaActual->modify('-1 day');

        // Formatear la fecha como una cadena en formato DateTime (por ejemplo, 'Y-m-d H:i:s')
        $diaAnterior = $fechaActual->format('Y-m-d');
        $diaAnteriorTitle = $fechaActual->format('d-m-Y');


         // Define las franjas horarias
         $timeRanges = [
            '11:00-12:00' => [11, 12],
            '12:00-13:00' => [12, 13],
            '13:00-14:00' => [13, 14],
            '14:00-15:00' => [14, 15],
            '15:00-16:00' => [15, 16],
            '16:00-17:00' => [16, 17],
            '17:00-18:00' => [17, 18],
            '18:00-19:00' => [18, 19],
            '19:00-20:00' => [19, 20],
            '20:00-21:00' => [20, 21],
            '21:00-22:00' => [21, 22],
            '22:00-23:00' => [22, 23],
            '23:00-00:00' => [23, 00],
            // Agrega más franjas según sea necesario
        ];

        // Define las zonas (esto dependerá de tu estructura de datos)
        $zones = ['zona1', 'zona2']; // Modifica esto según tus zonas

        $allChartData = [];

        foreach ($zonas as $zone) {
            $chartData = [
                'labels' => array_keys($timeRanges),
                'datasets' => [
                    [
                        'label' => 'Total comensales',
                        'data' => [],
                        'borderColor' => 'rgb(75, 192, 192)',
                        'backgroundColor' => 'rgb(75, 192, 192)',
                        'yAxisID' => 'y',
                    ]
                ]
            ];

            
            foreach ($timeRanges as $rangeLabel => $range) {
                $comensales = $this->statsRepository->findComensalesByTimeRangeAndZone($diaAnterior, $range[0], $range[1], $zone->getId());
                // Extraer el valor de total_comensales del resultado
                $totalComensales = isset($comensales[0]['total_comensales']) ? (int)$comensales[0]['total_comensales'] : 0;

                $chartData['datasets'][0]['data'][] = $totalComensales;
            }

            // Almacena cada array de zona en el array principal con su nombre de clave
            $allChartData[$zone->getName()] = $chartData;
        }


        //MEDIA DE COMENSALES POR DIAS

        // Obtener la fecha de hoy
            $hoy = new DateTime();

            // Crear un array con las fechas de los últimos 7 días
            $dataDays = array();
            for ($i = 0; $i < 7; $i++) {
                $dataDays[$hoy->format('Y-m-d')] = $hoy->format('l'); // Agregar la fecha y el nombre del día de la semana al array
                $hoy->modify('-1 day'); // Retroceder al día anterior
            }

            $dataDays = array_reverse($dataDays);

            // print_r(count($dataDays));



        $allChartDataDays = [];

        foreach ($zonas as $zone) {
            $chartData = [
                'labels' => array_keys($dataDays),
                'datasets' => [
                    [
                        'label' => 'Comensales por día',
                        'data' => [],
                        'borderColor' => 'rgb(255, 102, 135)',
                        'backgroundColor' => 'rgb(255, 102, 135)',
                        'yAxisID' => 'y',
                    ]
                ]
            ];

            $count = 1;
            foreach ($dataDays as $day => $d ) {
                $comensales = $this->statsRepository->findComensalesByDayAndZone($day, $zone->getId());
                // Extraer el valor de total_comensales del resultado
                $totalComensales = isset($comensales[0]['promedio_comensales']) ? (int)$comensales[0]['promedio_comensales'] : 0;

                $chartData['datasets'][0]['data'][] = $totalComensales;
                $count++;
            }

            // Almacena cada array de zona en el array principal con su nombre de clave
            $allChartDataDays[$zone->getId()] = $chartData;
        }

        $allComensales = $this->statsRepository->allComensalesPerDay();

        $data = array();
        foreach($allComensales as $all){

            $fecha = new DateTime($all['fecha']);
            $date = $fecha ? $fecha->format('d-m-Y') : '';
                $temp = array(
                    'id' => $all['id'],
                    'fecha' => $date,
                    'total_comensales' => $all['total_comensales'],
                );

                    array_push($data, $temp);

        }




        return $this->render('stats_comensales/index.html.twig', [
            'controller_name' => 'StatsComensalesController',
            'zonas' => $zonas,
            'chartData' => $allChartData,
            'chartDataDays' => $allChartDataDays,
            'diaanterior' => $diaAnteriorTitle,
            'allcomensales' => $data,
        ]);
    }

    /**
     * @Route("/actualizarfecha", name="actualizar_fecha")
     */
    public function actualizarFecha(Request $request): Response
    {

        $date = $request->request->get('date');
        $zona = $request->request->get('zona');



        $zonaId = $this->entityManager->getRepository(Zonas::class)->findOneBy(['name' => $zona])->getId();

        // $newDate = new DateTime($date);

        $timeRanges = [
            '11:00-12:00' => [11, 12],
            '12:00-13:00' => [12, 13],
            '13:00-14:00' => [13, 14],
            '14:00-15:00' => [14, 15],
            '15:00-16:00' => [15, 16],
            '16:00-17:00' => [16, 17],
            '17:00-18:00' => [17, 18],
            '18:00-19:00' => [18, 19],
            '19:00-20:00' => [19, 20],
            '20:00-21:00' => [20, 21],
            '21:00-22:00' => [21, 22],
            '22:00-23:00' => [22, 23],
            '23:00-00:00' => [23, 00],
            // Agrega más franjas según sea necesario
        ];



            $chartData = [
                'labels' => array_keys($timeRanges),
                'datasets' => [
                    [
                        'label' => 'Total comensales',
                        'data' => [],
                        'borderColor' => 'rgb(75, 192, 192)',
                        'backgroundColor' => 'rgb(75, 192, 192)',
                        'yAxisID' => 'y',
                    ]
                ]
            ];

            
            foreach ($timeRanges as $rangeLabel => $range) {
                $comensales = $this->statsRepository->findComensalesByTimeRangeAndZone($date, $range[0], $range[1], $zonaId);
                // Extraer el valor de total_comensales del resultado
                $totalComensales = isset($comensales[0]['total_comensales']) ? (int)$comensales[0]['total_comensales'] : 0;

                $chartData['datasets'][0]['data'][] = $totalComensales;
            }
        


       




        return new JsonResponse([

            'newChart' => $chartData,
            
        ]);
    }
}
