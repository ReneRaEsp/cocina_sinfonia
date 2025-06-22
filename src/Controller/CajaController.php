<?php

namespace App\Controller;

use App\Entity\Caja;
use App\Entity\Cajaregistro;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use DateTime;

use function PHPUnit\Framework\isEmpty;

class CajaController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    /**
     * @Route("/caja", name="caja")
     */
    public function index(): Response
    {

        // Obtener la fecha actual
        $fechaActual = new \DateTime();
        $fechaActual2 = new \DateTime();
        $fechaDiaAnterior = $fechaActual2->modify('-1 day');
        $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));
        $fechaDiaAnterior_sinHora = new \DateTime($fechaDiaAnterior->format('Y-m-d'));
        $objetoDiaActual = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);
        $objetoDiaAnterior = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' =>  $fechaDiaAnterior_sinHora]);


        $totalCaja = 0;

        if ($objetoDiaActual) {

            $totalCaja = $objetoDiaActual->getTotalCaja();
        }

        $registros = $this->entityManager->getRepository(Cajaregistro::class)->findAll();



        $data = array();
        foreach ($registros as $registro) {
            $lastLogin = $registro->getDia();
            $date = $lastLogin ? $lastLogin->format('d-m-Y') : '';
            $temp = array(
                'id' => $registro->getId(),
                'dia' => $date,
                'inicio' => $registro->getInicioCaja(),
                'final' => $registro->getFinalCaja(),
                'totalcalculado' => $registro->getTotalCaja(),
                'descuadre' => $registro->getDescuadre(),
                'observaciones' => $registro->getObservaciones(),
            );

            array_push($data, $temp);
        }

        if (isset($objetoDiaActual)) {

            $inicioCaja = '';
        } else if (isset($objetoDiaAnterior)) {
            $inicioCaja = $objetoDiaAnterior->getTotalCaja();
        } else {
            $inicioCaja = '';
        }



        // Luego, puedes pasar el objeto del día actual a la plantilla Twig para renderizarla
        return $this->render('caja/index.html.twig', [
            'controller_name' => 'CajaController',
            'inicio' => $objetoDiaActual  ? $objetoDiaActual->getInicioCaja() : '',
            'final' => $objetoDiaActual ? $objetoDiaActual->getFinalCaja() : '',
            'ganancia' => $totalCaja,
            'registros' => $data,
            'cierreayer' => $inicioCaja,
        ]);
    }
    /**
     * @Route("/insertcaja", name="insertcaja")
     */
    public function insertCaja(Request $request): Response
    {
        $inicio = $request->request->get('inicio');
        $fecha_actual = new DateTime();
        $fecha_sin_hora = new \DateTime($fecha_actual->format('Y-m-d'));

        $caja = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);


        if (isset($caja) && !empty($caja)) {

            if (!empty($inicio)) {
                $caja->setIniciocaja($inicio);
            }

            $this->entityManager->persist($caja);
            $this->entityManager->flush();
        } else {

            $cajaFinal = new Cajaregistro();
            (!empty($inicio)) ?  $cajaFinal->setIniciocaja($inicio) : null;
            (!empty($fin)) ?   $cajaFinal->setFinalcaja($fin) : null;
            $cajaFinal->setDia($fecha_sin_hora);

            $this->entityManager->persist($cajaFinal);
            $this->entityManager->flush();
        }

        return new JsonResponse([]);
    }

    /**
     * @Route("/cerrarcaja", name="cerrarcaja")
     */
    public function cerrarCaja(Request $request): Response
    {
        $fin = $request->request->get('final');
        $fecha_actual = new DateTime();
        $fecha_sin_hora = new \DateTime($fecha_actual->format('Y-m-d'));

        $caja = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);


        if (isset($caja) && !empty($caja)) {

            if (!empty($fin)) {
                $caja->setFinalcaja($fin);
            }

            $descuadreCaja = $caja->getFinalCaja() - $caja->getTotalCaja();
            $totalCalculado = $caja->getTotalCaja();

            $caja->setDescuadre($descuadreCaja);

            $this->entityManager->persist($caja);
            $this->entityManager->flush();
        } else {

            $cajaFinal = new Cajaregistro();
            (!empty($inicio)) ?  $cajaFinal->setIniciocaja($inicio) : null;
            (!empty($fin)) ?   $cajaFinal->setFinalcaja($fin) : null;
            $cajaFinal->setDia($fecha_sin_hora);

            $this->entityManager->persist($cajaFinal);
            $this->entityManager->flush();
        }




        return new JsonResponse([
            'descuadre' => $descuadreCaja,
            'calculado' => $totalCalculado,


        ]);
    }

    /**
     * @Route("/añadircaja", name="añadircaja")
     */
    public function añadirCaja(Request $request): Response
    {
        $cantidad = $request->request->get('cantidad');
        $fecha_actual = new DateTime();
        $fecha_sin_hora = new \DateTime($fecha_actual->format('Y-m-d'));

        $caja = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);


        if (isset($caja) && !empty($caja)) {


            $caja->setIniciocaja($caja->getIniciocaja() + floatval($cantidad));
            $caja->setObservaciones('Se ha sumado a la caja: ' . floatval($cantidad) . ' €  Del total calculado');




            $this->entityManager->persist($caja);
            $this->entityManager->flush();
        } else {

            return new JsonResponse([
                'message' => 'La caja no esta iniciada',


            ]);
        }




        return new JsonResponse([
            'message' => 'Datos guardados correctamente',


        ]);
    }

    /**
     * @Route("/restarCaja", name="restarCaja")
     */
    public function restarCaja(Request $request): Response
    {
        $cantidad = $request->request->get('cantidad');
        $fecha_actual = new DateTime();
        $fecha_sin_hora = new \DateTime($fecha_actual->format('Y-m-d'));

        $caja = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);


        if (isset($caja) && !empty($caja)) {


            $caja->setIniciocaja($caja->getIniciocaja() - floatval($cantidad));
            $caja->setObservaciones('Se ha restado a la caja: ' . floatval($cantidad) . ' € Del total calculado');


            $this->entityManager->persist($caja);
            $this->entityManager->flush();
        } else {

            return new JsonResponse([
                'message' => 'La caja no esta iniciada',


            ]);
        }




        return new JsonResponse([
            'message' => 'Datos guardados correctamente',


        ]);
    }
}
