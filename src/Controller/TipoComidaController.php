<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TipoComidaController extends AbstractController
{
    /**
     * @Route("/tipo/comida", name="app_tipo_comida")
     */
    public function index(): Response
    {
        return $this->render('tipo_comida/index.html.twig', [
            'controller_name' => 'TipoComidaController',
        ]);
    }
}
