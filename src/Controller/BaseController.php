<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personalizacion;

class BaseController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/base", name="app_base")
     */
    public function index(): Response
    {
        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }

    /**
 * @Route("/get_tabulator_route", name="get_tabulator_route")
 */
public function getTabulatorRouteFromDatabase()
{
    $repoPers = $this->entityManager->getRepository(Personalizacion::class);
    $personalizacion = $repoPers->findOneBy(['active' => true]);

    return new JsonResponse(['tabulatorRoute' => $personalizacion->getPath()]);
}
}
