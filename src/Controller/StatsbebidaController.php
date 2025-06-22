<?php

namespace App\Controller;

use App\Entity\Comida;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Ventas;
use App\Repository\StatscomidaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class StatsbebidaController extends AbstractController
{

    private $entityManager;
    private $statsRepository;

    public function __construct(EntityManagerInterface $entityManager, StatscomidaRepository $statsRepository)
    {
        $this->entityManager = $entityManager;
        $this->statsRepository = $statsRepository;
    }
    /**
     * @Route("/statsbebida", name="statsbebida")
     */
    public function index(): Response
    {

        $allBebida = $this->entityManager->getRepository(Comida::class)->findAll();

        $data_all = [];

        foreach ($allBebida as $bebida) {
            if ($bebida->isIsBebida()) {
                $temp = [
                    $bebida->getName() => $bebida->getId(),

                ];
                $data_all =  array_merge($data_all, $temp);
            }
        }

        //Array mas de todos los platos mas pedidos
        $all = $this->statsRepository->allWithRepetitionsSodas();

        $labels = [];
        $data = [];
        $backgroundColor = [
            'rgba(255, 99, 132, 0.8)',   // Bright Red
            'rgba(255, 159, 64, 0.8)',   // Bright Orange
            'rgba(255, 205, 86, 0.8)',   // Bright Yellow
            'rgba(75, 192, 192, 0.8)',   // Bright Green
            'rgba(54, 162, 235, 0.8)',   // Bright Blue
            'rgba(153, 102, 255, 0.8)',  // Bright Purple
            'rgba(201, 203, 207, 0.8)',  // Bright Grey
            // Add more colors if needed
        ];

        foreach ($all as $stat) {
            $comidaNombre = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $stat['comida_id']])->getName();
            $labels[] = $comidaNombre;
            $data[] = $stat['num_repeticiones'];
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $data,
                    'backgroundColor' => $backgroundColor
                ]
            ]
        ];


        //Array de todos los pedidos por fecha
        $topDishesPerMonth = $this->statsRepository->getTopSodaPerMonth();

        $dataMonths = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $backgroundColor = [
            'rgba(255, 99, 132, 0.8)',   // Bright Red
            'rgba(255, 159, 64, 0.8)',   // Bright Orange
            'rgba(255, 205, 86, 0.8)',   // Bright Yellow
            'rgba(75, 192, 192, 0.8)',   // Bright Green
            'rgba(54, 162, 235, 0.8)',   // Bright Blue
            'rgba(153, 102, 255, 0.8)',  // Bright Purple
            'rgba(201, 203, 207, 0.8)',  // Bright Grey
            // Add more colors if needed
        ];

        $chartDataMonth = [
            'labels' => $dataMonths,
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => array_fill(0, count($dataMonths), 0), // Inicializamos con 0 para cada mes
                    'dishNames' => array_fill(0, count($dataMonths), ''),
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 1,
                    'fill' => false,
                ],
            ],
        ];

        foreach ($topDishesPerMonth as $dish) {
            $monthIndex = array_search($dish['mes'], $dataMonths); // Buscamos el índice del mes
            if ($monthIndex !== false) {
                $chartDataMonth['datasets'][0]['data'][$monthIndex] = (int)$dish['num_repeticiones']; // Actualizamos el valor
                $chartDataMonth['datasets'][0]['dishNames'][$monthIndex] = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $dish['comida_id']])->getName();
            }
        }

        //Platos mas  pedidos ultima semana

        $topPlatos = $this->statsRepository->findTop5SodasLastWeek();

        // Últimos 7 días para el eje X
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = (new \DateTime("-$i days"))->format('Y-m-d');
        }

        $colores = [
            'rgb(255, 206, 93)',  // Amarillo
            'rgb(78, 216, 216)',  // Verde
            'rgb(65, 111, 141)',  // Azul
            'rgb(255, 166, 77)',  // Rojo
            'rgb(255, 161, 181)'
            // Agrega más colores si lo deseas...
        ];

        $datasets = [];
        $colorNumber = 0;
        foreach ($topPlatos as $plato) {
            $data = array_fill(0, 7, 0);
            foreach ($plato['pedidosDiarios'] as $pedido) {
                $dayIndex = array_search($pedido['fecha'], $days);
                if ($dayIndex !== false) {
                    $data[$dayIndex] = $pedido['pedidos'];
                }
            }

            // Seleccionar un color aleatorio
            $backgroundColor = $colores[$colorNumber];

            $datasets[] = [
                'label' => $plato['nombre'],
                'data' => $data,
                'borderColor' => $backgroundColor,
                'backgroundColor' => $backgroundColor,
            ];

            $colorNumber++;
        }

        $chartWeek = [
            'labels' => $days,
            'datasets' => $datasets,
        ];

        $allSodaRepetitions = $this->statsRepository->allSodaRepetitions();

        $data = array();
        foreach($allSodaRepetitions as $all){

            $fecha = new DateTime($all['dia']);
            $date = $fecha ? $fecha->format('d-m-Y') : '';
                $temp = array(
                    'id' => $all['id'],
                    'dia' => $date,
                    'name' => $all['name'],
                    'total_registros' => $all['total_registros'],
                );

                    array_push($data, $temp);

        }
        return $this->render('statsbebida/index.html.twig', [
            'chartData' => json_encode($chartData),
            'chartDataMonth' => json_encode($chartDataMonth),
            'chartWeek' => json_encode($chartWeek),
            'allbebida' => $data_all,
            'allbebidarepetitions' => $data
        ]);
    }

    /**
     * @Route("/bebidasemana", name="bebida_semana")
     */
    function buscar(Request $request): Response
    {

        $idComida = $request->request->get('id');


        $platoSemana = $this->statsRepository->findSodaLastWeek($idComida);



        // Últimos 7 días para el eje X
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = (new \DateTime("-$i days"))->format('Y-m-d');
        }

        $colores = [
            'rgb(255, 206, 93)',  // Amarillo
            'rgb(78, 216, 216)',  // Verde
            'rgb(65, 111, 141)',  // Azul
            'rgb(255, 166, 77)',  // Rojo
            'rgb(255, 161, 181)'
            // Agrega más colores si lo deseas...
        ];

        $datasets = [];
        $colorNumber = 0;
        foreach ($platoSemana as $plato) {
            $data = array_fill(0, 7, 0);
            foreach ($plato['pedidosDiarios'] as $pedido) {
                $dayIndex = array_search($pedido['fecha'], $days);
                if ($dayIndex !== false) {
                    $data[$dayIndex] = $pedido['pedidos'];
                }
            }

            // Seleccionar un color aleatorio
            $backgroundColor = $colores[$colorNumber];

            $datasets[] = [
                'label' => $plato['nombre'],
                'data' => $data,
                'borderColor' => $backgroundColor,
                'backgroundColor' => $backgroundColor,
            ];

            $colorNumber++;
        }

        $chartWeek = [
            'labels' => $days,
            'datasets' => $datasets,
        ];



        return new JsonResponse([
            'chartDishWeek' => $chartWeek

        ]);
    }
    /**
     * @Route("/restablecersemanabebida", name="restablecer_semana_bebida")
     */
    function restablecerSemana(Request $request): Response
    {

        $idComida = $request->request->get('id');


        $platoSemana = $this->statsRepository->findTop5SodasLastWeek();



        // Últimos 7 días para el eje X
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $days[] = (new \DateTime("-$i days"))->format('Y-m-d');
        }

        $colores = [
            'rgb(255, 206, 93)',  // Amarillo
            'rgb(78, 216, 216)',  // Verde
            'rgb(65, 111, 141)',  // Azul
            'rgb(255, 166, 77)',  // Rojo
            'rgb(255, 161, 181)'
            // Agrega más colores si lo deseas...
        ];

        $datasets = [];
        $colorNumber = 0;
        foreach ($platoSemana as $plato) {
            $data = array_fill(0, 7, 0);
            foreach ($plato['pedidosDiarios'] as $pedido) {
                $dayIndex = array_search($pedido['fecha'], $days);
                if ($dayIndex !== false) {
                    $data[$dayIndex] = $pedido['pedidos'];
                }
            }

            // Seleccionar un color aleatorio
            $backgroundColor = $colores[$colorNumber];

            $datasets[] = [
                'label' => $plato['nombre'],
                'data' => $data,
                'borderColor' => $backgroundColor,
                'backgroundColor' => $backgroundColor,
            ];

            $colorNumber++;
        }

        $chartWeek = [
            'labels' => $days,
            'datasets' => $datasets,
        ];



        return new JsonResponse([
            'chartDishWeek' => $chartWeek

        ]);
    }
}
