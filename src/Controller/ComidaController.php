<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Comida;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class ComidaController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

    }
    /**
     * @Route("/comida", name="app_comida")
     */
    public function index(): Response
    {
        return $this->render('comida/index.html.twig', [
            'controller_name' => 'ComidaController',
        ]);
    }

    /**
     * @Route("/searchcomida", name="search_comida")
     */
    public function searchComida(Request $request){

        $em = $this->getDoctrine()->getManager();
        $id_type = $request->request->get('id_type');

        $foods = $em->getRepository(Comida::class)->findBy(['type_food' => $id_type]);

        $foods_response = array();
        foreach($foods as $food){
            if(!$food->isIsDeleted()){
            $arrayExtras = array();
            $extras = $food->getPosiblesExtras();
            $repoComida = $this->entityManager->getRepository(Comida::class);
            foreach($extras as $extra){
                $idExtras = $repoComida->findOneBy(['id' => $extra]);
                $arrayExtras[] = array(
                    "nombre" => $idExtras->getName(),
                    "precio" => $idExtras->getPrecio(),
                );

            }
            $temp = array(
                'id' => $food->getId(),
                'name' => $food->getName(),
                'precio' => $food->getPrecio(),
                'extras' => $arrayExtras,
                'ordenplato' => $food->getNumPlato(),
                'img' => $food->getRutaimg() ? 'foodimg/'.$food->getRutaimg() : '',
                
                );
                array_push($foods_response, $temp);

            }

            }

        return new JsonResponse([
            'foods' => $foods_response
        ]);

    }

}
