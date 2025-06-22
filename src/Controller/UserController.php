<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
   
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'Hola Ivan',
        ]);
    }
    /**
     * @Route("/generar_venta", name="venta")
     */
    public function GenerarVenta(): Response
    {
        $stock = new Stock;
        $request = $request->json;
        // $stock->findOneBy(['referencia' => $request->get('venta')]);
    }
}
