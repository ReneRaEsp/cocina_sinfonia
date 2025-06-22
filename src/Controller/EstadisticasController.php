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


class EstadisticasController extends AbstractController
{
    private $entityManager;
    private $statsRepository;

    public function __construct(EntityManagerInterface $entityManager, StatscomidaRepository $statsRepository)
    {
        $this->entityManager = $entityManager;
        $this->statsRepository = $statsRepository;
    }


    /**
     * @Route("/estadisticas", name="estadisticas")
     */
    public function index(): Response
    {

        //Array mas de todos los platos mas pedidos
        $all = $this->statsRepository->allWithRepetitions();

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
        $topDishesPerMonth = $this->statsRepository->getTopDishPerMonth();

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
                    'label' => 'Plato más pedido',
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
                $chartDataMonth['datasets'][0]['dishNames'][$monthIndex] = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $dish['comida_id'] ])->getName();
            }
        }






        return $this->render('estadisticas/index.html.twig', [
            'chartData' => json_encode($chartData),
            'chartDataMonth' => json_encode($chartDataMonth),

        ]);
    }
}
