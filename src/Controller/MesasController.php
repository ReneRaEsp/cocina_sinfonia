<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Mesas;
use App\Entity\Pedidos;
use App\Entity\Comida;
use App\Entity\Stock;
use App\Entity\Ventas;
use App\Entity\Zonas;
use App\Entity\TipoComida;
use App\Entity\HistorialPedidos;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\TicketController;
use App\Entity\Cajaregistro;
use App\Entity\Impresoras;
use App\Entity\Info;
use App\Entity\Productostienda;
use App\Entity\Statscomida;
use App\Entity\Tickets;
use App\Entity\SunmiCloudPrinter;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Exception\ProcessFailedException;
use DateTime;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use App\Repository\MesasRepository;

use function PHPUnit\Framework\isNull;

class MesasController extends AbstractController
{
    private $entityManager;
    private $security;
    private $mesasRepository;
    private $impuesto;

    public function __construct(EntityManagerInterface $entityManager, Security $security, MesasRepository $mesasRepository)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->mesasRepository = $mesasRepository;
        $this->impuesto = 10;
    }

    /**
     * @Route("/", name="mesas")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $zona = $this->entityManager->getRepository(Zonas::class)->findOneBy(['id' => 1]);
        $fechaActual = new \DateTime();
        $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));
        $cajaAbierta = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);
        if ($zona !== null) {

            $mesasConComensales = $this->mesasRepository->mesasConComensales();

            $primerazona = $zona->getId();
            $mesas = $em->getRepository(Mesas::class)->findOneBy(['zonas' => $zona->getId()]);
            $mesasAll = $em->getRepository(Mesas::class)->findAll();
            // $barra = $em->getRepository(Mesas::class)->findOneBy(['localizacion' => 'Barra']);

            $repoMesas = $this->entityManager->getRepository(Mesas::class);
            $uniones = $repoMesas->findMesasUnidas();
            $mesas_libres = $repoMesas->mesasDisponibles();
            $mesas_con_union = $repoMesas->mesasConUniones(1);

            $id_mesas = array();
            if ($mesas) {

                foreach ($mesas as $mesa) {
                    $temp = array(
                        'numero' => $mesa->getNumero(),
                        'loc' => $mesa->getZona(),


                    );
                    array_push($id_mesas, $temp);
                }
            }

            $mesas_all = array();
            foreach ($mesasAll as $mesa) {
                $temp = array(
                    'id' => $mesa->getId(),
                    'numero' => $mesa->getNumero(),
                    'loc' => $mesa->getZonas()->getId(),


                );
                array_push($mesas_all, $temp);
            }

            $result_mesas = array();
            foreach ($mesas_con_union as $key => $value) {
                $result[] = "$key";
                $numbers = explode(",", $value);
                $result_mesas = array_merge($result, $numbers);
            }

            $total_mesas_comedor = $repoMesas->totalMesasComedor();
            $idsMesasComedor = $repoMesas->idMesasComedor();
            $idsMesasTerraza = $repoMesas->idMesasTerraza();

            $repoZonas = $this->entityManager->getRepository(Zonas::class);

            $zonaComedor = $repoZonas->findOneBy(['id' => 1]);
            $zonaTerraza = $repoZonas->findOneBy(['id' => 2]);
            $zonaBarra = $repoZonas->findOneBy(['id' => 3]);

            $zonas = $repoZonas->findAll();
            $array_zonas = [];
            $totalpagar_zonas = [];
            foreach ($zonas as $zona) {
                if ($zona->isActive()) {
                    $temp = array(
                        'id' => $zona->getId(),
                        'name' => $zona->getName(),
                        // 'icon' => $tipocomida->getIcon(),

                    );
                    array_push($array_zonas, $temp);

                    $result = $this->mesasRepository->totalZonas($zona->getId());
                    $temp2 = array(
                        'totalpagar' => $result[0]['total_porpagar'],
                        'zona' => $result[0]['zonas_id']

                    );

                    array_push($totalpagar_zonas, $temp2);
                }
            }



            $repoTipo = $this->entityManager->getRepository(TipoComida::class);
            $TiposComida = $repoTipo->findBy(['active' => true]);

            //List para movil
            $newTiposMovil = array();

            foreach ($TiposComida as $tipocomida) {
                $temp = array(
                    'id' => $tipocomida->getId(),
                    'name' => $tipocomida->getName(),
                    'icon' => $tipocomida->getIcon() ?? $tipocomida->getRutaImg(),

                );
                array_push($newTiposMovil, $temp);
            }

            //List para Desktop
            $newTipos = array();
            foreach ($TiposComida as $tipocomida) {
                $temp = array(
                    'id' => $tipocomida->getId(),
                    'name' => $tipocomida->getName(),
                    'icon' => $tipocomida->getIcon() ?? $tipocomida->getRutaImg(),

                );
                array_push($newTipos, $temp);
            }

            
            $tamanoTrozo = count($newTipos) > 0 ? ceil(count($newTipos) / 3) : 1;

            $tipos_separate = array_chunk($newTipos, $tamanoTrozo);
            
            $comidas_1 = isset($tipos_separate[0]) ? $tipos_separate[0] : [];
            $comidas_2 = isset($tipos_separate[1]) ? $tipos_separate[1] : []; 
            $comidas_3 = isset($tipos_separate[2]) ? $tipos_separate[2] : [];

            


            $repoComida = $this->entityManager->getRepository(Comida::class);
            $isComida = $repoComida->findBy(['iscomida' => 1]);

            $list_iscomida = array();

            foreach ($isComida as $comida) {
                $temp = array(
                    'id' => $comida->getId(),

                );
                array_push($list_iscomida, $temp);
            }
        } else {

            $id_mesas = null;
            $uniones = null;
            $mesas_libres = null;
            $mesas_con_union = null;
            $result_mesas = null;
            $total_mesas_comedor = null;
            // $barra = null; // Si esta línea estaba comentada, no es necesario asignar a null
            $idsMesasComedor = null;
            $idsMesasTerraza = null;
            $comedor = null;
            $terraza = null;
            $barra_zona = null;
            $array_zonas = null;
            $comidas_1 = null;
            $comidas_2 = null;
            $comidas_3 = null;
            $list_iscomida = null;
            $newTiposMovil = null;
            $newTipos = null;
            $mesas_all = null;
            $primerazona = null;
            $cajaCerrada = null;
            $mesasConComensales = null;
            $totalpagar_zonas = null;
        }

        $user = $this->getUser();

        if ($user) {
            // Verifica el rol del usuario actual
            $user = $this->security->getUser();
            if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_CAMARERO')) {
                if ($zona === null) {
                    return $this->render('mesas/index.html.twig', [
                        'controller_name' => 'MesasController',
                        'id_mesas' => $id_mesas,
                        'uniones' => $uniones,
                        'mesas_libres' => $mesas_libres,
                        'mesas_con_uniones' => $mesas_con_union,
                        'result' => $result_mesas,
                        'mesas_comedor' => $total_mesas_comedor,
                        // 'barra' => $barra->getNumero(),
                        'id_comedor' => $idsMesasComedor,
                        'id_terraza' => $idsMesasTerraza,
                        'comedor' => null,
                        'terraza' => null,
                        'barra_zona' => null,
                        'zonas' => $array_zonas,
                        'comida_1' => $comidas_1,
                        'comida_2' => $comidas_2,
                        'comida_3' => $comidas_3,
                        'is_comida' => $list_iscomida,
                        'comida_moviles' => $newTiposMovil,
                        'newcomidas' => $newTipos,
                        'primerazona' => $primerazona,
                        'zonasoff' => true,
                        'cajacerrada' => $cajaCerrada,
                        'mesasconcomensales' => $mesasConComensales,
                        'total_zonas' => $totalpagar_zonas,
                    ]);
                } else if ($result_mesas !== null) {
                    return $this->render('mesas/index.html.twig', [
                        'controller_name' => 'MesasController',
                        'id_mesas' => $id_mesas,
                        'uniones' => $uniones,
                        'mesas_libres' => $mesas_libres,
                        'mesas_con_uniones' => $mesas_con_union,
                        'result' => $result_mesas,
                        'mesas_comedor' => $total_mesas_comedor,
                        // 'barra' => $barra->getNumero(),
                        'id_comedor' => $idsMesasComedor,
                        'id_terraza' => $idsMesasTerraza,
                        // 'comedor' => $zonaComedor->isActive(),
                        // 'terraza' => $zonaTerraza->isActive(),
                        // 'barra_zona' => $zonaBarra->isActive(),
                        'zonas' => $array_zonas,
                        'comida_1' => $comidas_1,
                        'comida_2' => $comidas_2,
                        'comida_3' => $comidas_3,
                        'is_comida' => $list_iscomida,
                        'comida_moviles' => $newTiposMovil,
                        'newcomidas' => $newTipos,
                        'zonasoff' => false,
                        'mesasall' => $mesas_all,
                        'primerazona' => $primerazona,
                        'cajacerrada' => ($cajaAbierta !== null) ? true : false,
                        'mesasconcomensales' => $mesasConComensales,
                        'total_zonas' => $totalpagar_zonas,
                    ]);
                } else {
                    return $this->render('mesas/index.html.twig', [
                        'controller_name' => 'MesasController',
                        'id_mesas' => $id_mesas,
                        'uniones' => $uniones,
                        'mesas_libres' => $mesas_libres,
                        'mesas_con_uniones' => $mesas_con_union,
                        'result' => '',
                        'mesas_comedor' => $total_mesas_comedor,
                        // 'barra' => $barra->getNumero(),
                        'id_comedor' => $idsMesasComedor,
                        'id_terraza' => $idsMesasTerraza,
                        // 'comedor' => $zonaComedor->isActive(),
                        // 'terraza' => $zonaTerraza->isActive(),
                        // 'barra_zona' => $zonaBarra->isActive(),
                        'zonas' => $array_zonas,
                        'comida_1' => $comidas_1,
                        'comida_2' => $comidas_2,
                        'comida_3' => $comidas_3,
                        'is_comida' => $list_iscomida,
                        'comida_moviles' => $newTiposMovil,
                        'zonasoff' => false,
                        'mesasall' => $mesas_all,
                        'primerazona' => $primerazona,
                        'cajacerrada' => ($cajaAbierta !== null) ? true : false,
                        'mesasconcomensales' => $mesasConComensales,
                        'total_zonas' => $totalpagar_zonas,
                    ]);
                }
            } elseif ($user->hasRole('ROLE_GESTOR')) {

                return $this->redirectToRoute('facturas');
            }
        } else {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * @Route("/listcomidamesas", name="list_comida")
     */
    public function listComidaMesa(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $request->request->get('mesa_id')]);
        $idzona = $request->request->get('idzona');

        $repoTickets = $this->entityManager->getRepository(Tickets::class);
        $allTickets = $repoTickets->findAll();

        $arrayTickets = [];
        foreach ($allTickets as $ticket) {
            $temp = array(
                'id' => $ticket->getId(),
                'mesa' => $ticket->getMesaid()->getId(),
                'numeroticket' => $ticket->getNumeroticket(),
                'pedidos' => $ticket->getPedidos()

            );

            array_push($arrayTickets, $temp);
        }

        $datos_mesa = $mesa->getPedidos();

        $haypedidos = $this->entityManager->getRepository(Pedidos::class)->findBy(['mesa' => $request->request->get('mesa_id')]);

        if (count($datos_mesa) === 0) {

            if ($mesa->getPorPagar() !== 0) {

                $mesa->setPorPagar(0);
                $this->entityManager->persist($mesa);
                $this->entityManager->flush();
            }

            if ($mesa->getPagado() !== 0) {

                $mesa->setPagado(0);
                $this->entityManager->persist($mesa);
                $this->entityManager->flush();
            }
        }

        $control_mesa = null;
        $totalMesa = 0;

        $data_mesa = array();
        foreach ($datos_mesa as $p_mesa) {
            $precioExtra = 0;
            $extrasString = $p_mesa->getComida() ? $p_mesa->getComida()->getName() : $p_mesa->getProducttienda()->getNombre();
            if ($p_mesa->getExtras()) {
                $extrasString .= ' ->Extras: ';
                $arrayExtras = $p_mesa->getExtras();
                foreach ($arrayExtras as $extra) {
                    $repoComida = $this->entityManager->getRepository(Comida::class);
                    $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                    $precioExtra += floatval($comidaExtra->getPrecio());
                    $extrasString .= $comidaExtra->getName() . ', ';
                }
                $extrasString = rtrim($extrasString, ', ');
            }
            $precio = $p_mesa->getComida() ? $p_mesa->getComida()->getPrecio() + $precioExtra : $p_mesa->getProducttienda()->getPvp() + $precioExtra;
            $precioFinal = null;
            if ($p_mesa->isInvitacion()) {
                $precioFinal = 'Invitación';
            } else if ($p_mesa->getDescuento() !== null) {

                $descuentoPorcentaje = floatval($p_mesa->getDescuento()) / 100;
                $descuentoEuros = $precio * $descuentoPorcentaje;
                $precioFinal = $precio - $descuentoEuros;
                $totalMesa += $precioFinal;
            } else if ($p_mesa->getDescuentoEur() !== null) {

                $precioFinal = $precio - floatval($p_mesa->getDescuentoEur());
                $totalMesa += $precioFinal;
            }

            $numTicket = '';
            foreach ($arrayTickets as $item) {

                foreach ($item["pedidos"] as $pedido) {

                    if ($p_mesa->getId() === intval($pedido)) {

                        $numTicket = ' (T' . $item["numeroticket"] . ')';
                    }
                }
            }


            $temp = array(
                'comida' => $extrasString,
                'id' => $p_mesa->getComida() ? $p_mesa->getComida()->getId() : $p_mesa->getProducttienda()->getId(),
                'precio' => $precioFinal !== null ? $precioFinal : $precio,
                'marchando' => $p_mesa->isMarchando(),
                'pedido_mesa' => $p_mesa->getId(),
                'invitacion' => $p_mesa->isInvitacion() ? 1 : 0,
                'descP' => $p_mesa->getDescuento(),
                'descE' => $p_mesa->getDescuentoEur(),
                'numTicket' => $numTicket
            );
            array_push($data_mesa, $temp);
            // if ($precioFinal !== null) {
            //     $totalMesa +=  $precioFinal;
            // } else {
            //     $totalMesa += $precio;
            // }
        }

        $totalMesa = $mesa->getPorPagar();

        $pedidosRepo =  $this->entityManager->getRepository(Pedidos::class);
        $mesa_id = $request->request->get('mesa_id');

        if (!empty($data_mesa)) {
            $pedidos = $pedidosRepo->findBy([
                'mesa' => $mesa_id,
                'marchando' => 0
            ]);

            $control_mesa = (empty($pedidos)) ? 1 : 0;
        }


        $totalPagarZonas = $this->mesasRepository->totalZonas($idzona);

        return new JsonResponse([
            'pedido_mesa' => $data_mesa,
            'total_mesa' => $totalMesa,
            'control_mesa' => $control_mesa,
            'total_zona' => $totalPagarZonas
        ]);
    }
    /**
     * @Route("/listcomidamesasterraza", name="list_comida_terraza")
     */
    public function listComidaMesaTerraza(Request $request)
    {



        $mesa = $this->entityManager->getRepository(Mesas::class)->findOneBy(['numero' => $request->request->get('mesa_id')]);

        $datos_mesa = $mesa->getPedidos();

        if (empty($datos_mesa) || $datos_mesa === null) {

            if ($mesa->getPorPagar() !== 0) {

                $mesa->setPorPagar(0);
                $this->entityManager->persist($mesa);
                $this->entityManager->flush();
            }

            if ($mesa->getPagado() !== 0) {

                $mesa->setPagado(0);
                $this->entityManager->persist($mesa);
                $this->entityManager->flush();
            }
        }

        $control_mesa = null;

        $data_mesa = array();
        foreach ($datos_mesa as $p_mesa) {
            $temp = array(
                'comida' => $p_mesa->getComida()->getName(),
                'id' => $p_mesa->getComida()->getId(),
                'precio' => $p_mesa->getComida()->getPrecio(),

            );
            array_push($data_mesa, $temp);
        }

        $pedidosRepo =  $this->entityManager->getRepository(Pedidos::class);
        $mesa_id = $request->request->get('mesa_id');

        if (!empty($data_mesa)) {
            $pedidos = $pedidosRepo->findBy([
                'mesa' => $mesa_id,
                'marchando' => 0
            ]);

            $control_mesa = (empty($pedidos)) ? 1 : 0;
        }


        return new JsonResponse([
            'pedido_mesa' => $data_mesa,
            'total_mesa' => $mesa->getPorPagar(),
            'control_mesa' => $control_mesa

        ]);
    }
    /**
     * @Route("/listbarra", name="list_barra")
     */
    public function listBarra(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $request->request->get('mesa_id')]);


        $datos_mesa = $mesa->getPedidos();

        $data_mesa = array();
        foreach ($datos_mesa as $p_mesa) {
            $temp = array(
                'comida' => $p_mesa->getComida()->getName(),
                'id' => $p_mesa->getComida()->getId(),
                'precio' => $p_mesa->getComida()->getPrecio(),

            );
            array_push($data_mesa, $temp);
        }

        $repoZonas = $this->entityManager->getRepository(Zonas::class);

        $zonaComedor = $repoZonas->findOneBy(['id' => 1]);
        $zonaTerraza = $repoZonas->findOneBy(['id' => 2]);

        return new JsonResponse([
            'pedido_mesa' => $data_mesa,
            'total_mesa' => $mesa->getPorPagar(),
            'comedor' => $zonaComedor->IsActive(),
            'terraza' => $zonaTerraza->IsActive(),

        ]);
    }

    /**
     * @Route("/pagoparcial", name="pago_parcial")
     */
    public function pagoParcial(Request $request, EntityManagerInterface $entityManager)
    {

        // TO DO: Modificar las llamadas del Entity Manager a la nueva version de Symfony 5

        $em = $this->getDoctrine()->getManager(); //DEPRECATED
        // $myRepository = $this->$entityManager->getRepository(Mesas::class); //New version to get Doctrine in Symfony 5
        $repoHistorialPedidos = $this->entityManager->getRepository(HistorialPedidos::class);

        $num_mesa = $request->request->get('num_mesa');
        $idzona = intval($request->request->get('idzona'));
        $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $num_mesa]);
        $imagenUrl = $mesa->getIcon();
        $mesa_total = floatval($mesa->getPorPagar());

        $pedidos = $request->request->get('elementos');
        $pago = $request->request->get('metodo_pago');

        $pedidosRef = array();

        $a_pagar = 0;
        $list_ticket = array();

        foreach ($pedidos as $pedido) {

            $comida = $em->getRepository(Comida::class)->findOneBy(['id' => $pedido["itemId"]]);
            $comidaTienda = $em->getRepository(Productostienda::class)->findOneBy(['id' => $pedido["itemId"]]);
            $p = $this->entityManager->getRepository(Pedidos::class)->findOneBy(['id' => $pedido["pedidoDatos"]]);

            $precioExtra = 0;
            $extrasString = $p->getComida() ? $p->getComida()->getName() : $p->getProducttienda()->getNombre();
            if ($p->getExtras()) {
                $extrasString .= ' ->Extras: ';
                $arrayExtras = $p->getExtras();
                foreach ($arrayExtras as $extra) {

                    $repoComida = $this->entityManager->getRepository(Comida::class);
                    $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                    $precioExtra += floatval($comidaExtra->getPrecio());
                    $extrasString .= $comidaExtra->getName() . ', ';
                }
                $extrasString = rtrim($extrasString, ', ');
            }

            $precio = $precioExtra;

            if ($p->getDescuento()) {

                $descuento = floatval($p->getDescuento()) / 100;
                $descuentoEur = $comida->getPrecio() ? $comida->getPrecio() * $descuento : $comidaTienda->getPvp() * $descuento;
                $precio += $comida->getPrecio() ?  $comida->getPrecio() - $descuentoEur : $comidaTienda->getPvp() - $descuentoEur;
            } else if ($p->getDescuentoEur()) {
                $precio += $comida->getPrecio() ? $comida->getPrecio() - $p->getDescuentoEur() : $comidaTienda->getPvp() - $p->getDescuentoEur();
            } else {
                $precio += $comida->getPrecio() ? $comida->getPrecio() : $comidaTienda->getPvp();
            }

            // Verificar si la comida ya está en el array
            $found = false;
            foreach ($list_ticket as &$item) {
                if ($item['id'] === $comida->getId() ? $comida->getId() : $comidaTienda->getId()) {
                    $item['cantidad'] += 1;
                    $item['price'] += $precio;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $temp = array(
                    'id' => $comida->getId() ? $comida->getId() : $comidaTienda->getId(),
                    'name' => $comida->getName() ? $comida->getName() : $comidaTienda->getNombre(),
                    'price' => $precio,
                    'cantidad' => 1,
                );
                array_push($list_ticket, $temp);
            }

            if ($comida !== null) {
                $stock = $em->getRepository(Stock::class)->findOneBy(['comida' => $comida->getId()]);
                if (isset($stock)) {
                    if ($stock->getAmount() !== 0) {
                        $stock->setAmount($stock->getAmount() - 1);
                        $this->entityManager->persist($stock);
                        $this->entityManager->flush();
                    }
                }
            }

            if ($comidaTienda !== null) {
                $stock = $em->getRepository(Stock::class)->findOneBy(['producttienda' => $comidaTienda->getId()]);
                if (isset($stock)) {
                    if ($stock->getAmount() !== 0) {
                        $stock->setAmount($stock->getAmount() - 1);

                        $this->entityManager->persist($stock);
                        $this->entityManager->flush();
                    }
                }
            }


            $a_pagar += $precio;


            array_push($pedidosRef, $p->getNumRef());

            $descuento = $p->getDescuento();
            $descuento_eur = $p->getDescuentoEur();
            $invitacion = $p->isInvitacion();
            $precio = $p->getComida() ? $p->getComida()->getPrecio() : $p->getProducttienda()->getPvp();
            $precio_total = $precio;

            if ($descuento != null) {
                $desc = $precio * $descuento / 100;
                $precio_total = $precio - $desc;
            } else if ($descuento_eur != null) {
                $precio_total = $precio - $descuento_eur;
            } else if ($invitacion != null) {
                $precio_total = 0;
            }

            $fechaActual = new \DateTime();
            $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));

            $pedidoHistorial = array(
                'mesa' => $p->getMesa()->getNumero(),
                'comensales' => $p->getMesa()->getComensales(),
                'comida' =>  $p->getComida() ? $p->getComida()->getName() : $p->getProducttienda()->getNombre(),
                'precio' => $p->getComida() ? $p->getComida()->getPrecio() : $p->getProducttienda()->getPvp(),
                'precio_total' => $precio_total,
                'comentarios' => $p->getComentarios(),
                'extras' => $p->getExtras(),
                'invitacion' => $invitacion,
                'descuento' => $descuento,
                'descuento_eur' => $descuento_eur,
                'num_ref' => $p->getNumRef(),
                'fecha' => $fechaActual,
                'user' => $this->getUser(),
                'iva' => $this->impuesto,
            );
            $newPedidoHistorial = new HistorialPedidos();
            $newPedidoHistorial->setMesa($pedidoHistorial['mesa']);
            $newPedidoHistorial->setComida($pedidoHistorial['comida']);
            $newPedidoHistorial->setPrecio($pedidoHistorial['precio']);
            $newPedidoHistorial->setPrecioTotal($pedidoHistorial['precio_total']);
            $newPedidoHistorial->setComentarios($pedidoHistorial['comentarios']);
            $newPedidoHistorial->setExtras($pedidoHistorial['extras']);
            $newPedidoHistorial->setInvitacion($pedidoHistorial['invitacion']);
            $newPedidoHistorial->setFecha($pedidoHistorial['fecha']);
            $newPedidoHistorial->setComensales($pedidoHistorial['comensales']);
            $newPedidoHistorial->setUser($pedidoHistorial['user']);
            $newPedidoHistorial->setIva($pedidoHistorial['iva']);

            if ($pedidoHistorial['descuento']) {
                $newPedidoHistorial->setDescuento($pedidoHistorial['descuento']);
            }
            if ($pedidoHistorial['descuento_eur']) {
                $newPedidoHistorial->setDescuentoEur($pedidoHistorial['descuento_eur']);
            }
            $newPedidoHistorial->setNumRef($pedidoHistorial['num_ref']);


            $em->persist($newPedidoHistorial);
            $em->remove($p);
            $em->flush();
        }
        $pedidosRef = json_encode($pedidosRef);

        if ($pago === 'Efectivo') {
            $fechaActual = new \DateTime();
            $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));
            $objetoDiaActual = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);

            if ($objetoDiaActual->getTotalCaja() === null) {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getInicioCaja() + floatval($a_pagar));
            } else {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getTotalCaja() + floatval($a_pagar));
            }

            $this->entityManager->persist($objetoDiaActual);
            $this->entityManager->flush();
        }



        $ticketController = new TicketController($entityManager);
        $order = [
            'id' => 2,
            'date' => date("d/m/Y H:i:s"),
            'items' => $list_ticket,
            'totalAmount' => $a_pagar,
            'nummesa' => $num_mesa
        ];

        $content = $ticketController->generateTicket($order);
        // guarda los cambios en la base de datos
        // $this->$entityManager->flush(); New save in symfony 5

        $falta_pagar = $mesa_total - $a_pagar;


        $valorRestado = $a_pagar / (1 + $this->impuesto / 100);  // Precio sin IVA
        $cantidadImpuesto = $a_pagar - $valorRestado;  // IVA



        if ($a_pagar === $mesa_total) {
            $venta = new Ventas();
            $mesa->setPorPagar(sprintf("%.2f", 0));
            $mesa->setPagado(sprintf("%.2f", 0));
            $mesa->setFactura(null);
            $mesa->setComensales(0);

            //Al ver que la mesa se ha pagado todo correctamente generamos una venta
            $venta->setMesa($mesa);
            $venta->setFecha(new \DateTime);
            $venta->setPagado($valorRestado);
            $venta->setIva($this->impuesto);
            $venta->setImporteIva(number_format($cantidadImpuesto, 2));
            $venta->setPago($pago);
            $venta->setPedidosRef($pedidosRef);
            $repoTickets = $this->entityManager->getRepository(Tickets::class);
            $tickets = $repoTickets->findBy(['mesaid' => $mesa->getId()]);
            foreach ($tickets as $ticket) {
                $this->entityManager->remove($ticket);
                $em->flush();
            }
            $em->persist($venta);
            $em->flush();
        } else {
            $venta = new Ventas();
            $pagado = floatval($mesa->getPagado() + $a_pagar);
            $mesa->setPorPagar(sprintf("%.2f", $falta_pagar));
            $mesa->setPagado(sprintf("%.2f", $pagado));

            //Al ver que la mesa se ha pagado todo correctamente generamos una venta
            $venta->setMesa($mesa);
            $venta->setFecha(new \DateTime);
            $venta->setPagado($valorRestado);
            $venta->setIva($this->impuesto);
            $venta->setImporteIva(number_format($cantidadImpuesto, 2));
            $venta->setPago($pago);
            $venta->setPedidosRef($pedidosRef);
            $em->persist($venta);
            $em->flush();
        }

        $em->persist($mesa);
        $em->flush();

        $arrayComensales = $this->mesasRepository->mesasConComensales();

        $totalPagarZonas = $this->mesasRepository->totalZonas($idzona);

        return new JsonResponse([
            'falta_pagar' => number_format($falta_pagar, 2, ',', '.'),
            'mesa' => $num_mesa,
            'ticket_content' =>  $content,
            'total' => number_format($a_pagar, 2),
            'img' => $imagenUrl,
            'arraycomensales' => $arrayComensales,
            'total_zona' => $totalPagarZonas

        ]);
    }
    /**
     * @Route("/pagototal", name="pago_total")
     */
    public function pagoTotal(Request $request)
    {

        // $impuesto = 10;

        $repoMesas = $this->entityManager->getRepository(Mesas::class); //New version to get Doctrine in Symfony 5
        $repoPedidos = $this->entityManager->getRepository(Pedidos::class); //New version to get Doctrine in Symfony 5
        $repoHistorialPedidos = $this->entityManager->getRepository(HistorialPedidos::class);
        $num_mesa = $request->request->get('num_mesa');
        $comensales = $request->request->get('comensales');
        $idzona = intval($request->request->get('idzona'));
        $efectivo = floatval($request->request->get('efectivo'));
        $tarjeta = floatval($request->request->get('tarjeta'));
        $observaciones = $request->request->get('observaciones');
        $mesa = $repoMesas->findOneBy(['numero' => $request->request->get('num_mesa')]);
        $imagenUrl = $mesa->getIcon();
        $mesa_total = floatval($mesa->getPorPagar());

        $pedidos = $repoPedidos->findBy(['mesa' => $mesa->getId()]);
        $pago = $request->request->get('metodo_pago');
        $list_ticket = array();
        $pedidosRef = array();
        foreach ($pedidos as $pedido) {
            $comidaTienda = null;
            $comida = null;
            if ($pedido->getComida() === null) {
                $comidaTienda = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $pedido->getProducttienda()->getId()]);
            } else {
                $comida = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $pedido->getComida()->getId()]);
            }


            $precioExtra = 0;
            $extrasString = $pedido->getComida() ? $pedido->getComida()->getName() : $pedido->getProducttienda()->getNombre();
            if ($pedido->getExtras()) {
                $extrasString .= ' ->Extras: ';
                $arrayExtras = $pedido->getExtras();
                foreach ($arrayExtras as $extra) {
                    $repoComida = $this->entityManager->getRepository(Comida::class);
                    $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                    $precioExtra += floatval($comidaExtra->getPrecio());
                    $extrasString .= $comidaExtra->getName() . ', ';
                }
                $extrasString = rtrim($extrasString, ', ');
            }

            $precio = $precioExtra;

            if ($pedido->getDescuento() !== null) {

                $descuento = floatval($pedido->getDescuento()) / 100;
                $descuentoEur = $comida ?  $comida->getPrecio() * $descuento : $comidaTienda->getPvp() * $descuento;
                $precio += $comida ? $comida->getPrecio() - $descuentoEur : $comidaTienda->getPvp() - $descuentoEur;
            } else if ($pedido->getDescuentoEur()) {
                $precio += $comida ? $comida->getPrecio()  - $pedido->getDescuentoEur() : $comidaTienda->getPvp()  - $pedido->getDescuentoEur();
            } else {
                $precio += $comida ? $comida->getPrecio() : $comidaTienda->getPvp();
            }

            // Verificar si la comida ya está en el array
            $found = false;
            foreach ($list_ticket as &$item) {
                if ($item['id'] === ($comida ? $comida->getId() : ($comidaTienda ? $comidaTienda->getId() : null))) {
                    $item['cantidad'] += 1;
                    $item['price'] += $precio;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $temp = array(
                    'id' => $comida ? $comida->getId() : $comidaTienda->getId(),
                    'name' => $comida ? $comida->getName() : $comidaTienda->getNombre(),
                    'price' => $precio,
                    'cantidad' => 1,
                );
                array_push($list_ticket, $temp);
            }


            if ($comida !== null) {
                $stock = $this->entityManager->getRepository(Stock::class)->findOneBy(['comida' => $comida->getId()]);
                if (isset($stock)) {
                    if ($stock->getAmount() !== 0) {

                        $stock->setAmount($stock->getAmount() - 1);
                        $this->entityManager->persist($stock);
                        $this->entityManager->flush();
                    }
                }
            }

            if ($comidaTienda !== null) {
                $stock = $this->entityManager->getRepository(Stock::class)->findOneBy(['producttienda' => $comidaTienda->getId()]);
                if (isset($stock)) {
                    if ($stock->getAmount() !== 0) {
                        $stock->setAmount($stock->getAmount() - 1);

                        $this->entityManager->persist($stock);
                        $this->entityManager->flush();
                    }
                }
            }

            array_push($pedidosRef, $pedido->getNumRef());

            $descuento = $pedido->getDescuento();
            $descuento_eur = $pedido->getDescuentoEur();
            $invitacion = $pedido->isInvitacion();
            $precio = $pedido->getComida() ? $pedido->getComida()->getPrecio() : $pedido->getProducttienda()->getPvp();
            $precio_total = $precio;

            if ($descuento != null) {
                $desc = $precio * $descuento / 100;
                $precio_total = $precio - $desc;
            } else if ($descuento_eur != null) {
                $precio_total = $precio - $descuento_eur;
            } else if ($invitacion != null) {
                $precio_total = 0;
            }

            $fechaActual = new \DateTime();

            $stats = new Statscomida();

            $stats->setFecha($fechaActual);
            $stats->setMesa($mesa);
            if ($comidaTienda !== null) {
                $stats->setTienda($comidaTienda);
            } else {
                $stats->setComida($comida);
                $stats->setTipoComida($comida->getTypeFood());
            }

            $this->entityManager->persist($stats);
            $this->entityManager->flush();



            $pedidoHistorial = array(
                'mesa' => $pedido->getMesa()->getNumero(),
                'comensales' => $pedido->getMesa()->getComensales(),
                'comida' =>  $pedido->getComida() ? $pedido->getComida()->getName() : $pedido->getProducttienda()->getNombre(),
                'precio' => $pedido->getComida() ? $pedido->getComida()->getPrecio() : $pedido->getProducttienda()->getPvp(),
                'precio_total' => $precio_total,
                'comentarios' => $pedido->getComentarios(),
                'extras' => $pedido->getExtras(),
                'invitacion' => $invitacion,
                'descuento' => $descuento,
                'descuento_eur' => $descuento_eur,
                'num_ref' => $pedido->getNumRef(),
                'fecha' => $fechaActual,
                'user' => $this->getUser(),
                'iva' => $this->impuesto,
            );
            $newPedidoHistorial = new HistorialPedidos();
            $newPedidoHistorial->setMesa($pedidoHistorial['mesa']);
            $newPedidoHistorial->setComida($pedidoHistorial['comida']);
            $newPedidoHistorial->setPrecio($pedidoHistorial['precio']);
            $newPedidoHistorial->setPrecioTotal($pedidoHistorial['precio_total']);
            $newPedidoHistorial->setComentarios($pedidoHistorial['comentarios']);
            $newPedidoHistorial->setExtras($pedidoHistorial['extras']);
            $newPedidoHistorial->setInvitacion($pedidoHistorial['invitacion']);
            $newPedidoHistorial->setFecha($pedidoHistorial['fecha']);
            $newPedidoHistorial->setComensales($pedidoHistorial['comensales']);
            $newPedidoHistorial->setUser($pedidoHistorial['user']);
            $newPedidoHistorial->setIva($pedidoHistorial['iva']);
            if ($pedidoHistorial['descuento']) {
                $newPedidoHistorial->setDescuento($pedidoHistorial['descuento']);
            }
            if ($pedidoHistorial['descuento_eur']) {
                $newPedidoHistorial->setDescuentoEur($pedidoHistorial['descuento_eur']);
            }
            $newPedidoHistorial->setNumRef($pedidoHistorial['num_ref']);

            $this->entityManager->persist($newPedidoHistorial);
            $this->entityManager->remove($pedido);
            $this->entityManager->flush();
        }
        $pedidosRef = json_encode($pedidosRef);

        $ticketController = new TicketController($this->entityManager);
        $order = [
            'id' => 2,
            'date' => date("d/m/Y H:i:s"),
            'items' => $list_ticket,
            'totalAmount' => $mesa_total,
            'nummesa' => $mesa->getNumero()
        ];

        $content = $ticketController->generateTicket($order);
        $this->entityManager->flush();



        if ($efectivo) {

            $valorRestado = $efectivo / (1 + $this->impuesto / 100);  // Precio sin IVA
            $cantidadImpuesto = $efectivo - $valorRestado;  // IVA
            if ($mesa_total !== 0) {

                $ventaEfectivo = new Ventas();
                $ventaEfectivo->setMesa($mesa);
                $ventaEfectivo->setFecha(new \DateTime);
                $ventaEfectivo->setPagado($valorRestado);
                $ventaEfectivo->setIva($this->impuesto);
                $ventaEfectivo->setImporteIva(number_format($cantidadImpuesto, 2));
                $ventaEfectivo->setPago('Efectivo');
                $ventaEfectivo->setNumMesa($num_mesa);
                $ventaEfectivo->setComesales(intval($request->request->get('comensales')));
                $ventaEfectivo->setPedidosRef($pedidosRef);
                $ventaEfectivo->setObservaciones($observaciones);
            } else {
                $ventaEfectivo = new Ventas();
                $ventaEfectivo->setMesa($mesa);
                $ventaEfectivo->setFecha(new \DateTime);
                $ventaEfectivo->setPagado($valorRestado);
                $ventaEfectivo->setIva($this->impuesto);
                $ventaEfectivo->setImporteIva(number_format($cantidadImpuesto, 2));
                $ventaEfectivo->setPago('Efectivo');
                $ventaEfectivo->setNumMesa($num_mesa);
                $ventaEfectivo->setComesales(intval($request->request->get('comensales')));
                $ventaEfectivo->setPedidosRef($pedidosRef);
                $ventaEfectivo->setObservaciones($observaciones);
            }

            $fechaActual = new \DateTime();
            $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));
            $objetoDiaActual = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);

            if ($objetoDiaActual->getTotalCaja() === null) {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getInicioCaja() + $efectivo);
            } else {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getTotalCaja() + $efectivo);
            }

            $this->entityManager->persist($objetoDiaActual);

            $this->entityManager->persist($ventaEfectivo);
        }


        if ($tarjeta) {

            $valorRestado = $tarjeta / (1 + $this->impuesto / 100);  // Precio sin IVA
            $cantidadImpuesto = $tarjeta - $valorRestado;  // IVA

            if ($mesa_total !== 0) {
                $ventaTarjeta = new Ventas();
                $ventaTarjeta->setMesa($mesa);
                $ventaTarjeta->setFecha(new \DateTime);
                $ventaTarjeta->setPagado($valorRestado);
                $ventaTarjeta->setIva($this->impuesto);
                $ventaTarjeta->setImporteIva(number_format($cantidadImpuesto, 2));
                $ventaTarjeta->setPago('Tarjeta');
                $ventaTarjeta->setNumMesa($num_mesa);
                $ventaTarjeta->setComesales(intval($request->request->get('comensales')));
                $ventaTarjeta->setPedidosRef($pedidosRef);
                $ventaTarjeta->setObservaciones($observaciones);
            } else {
                $ventaTarjeta = new Ventas();
                $ventaTarjeta->setMesa($mesa);
                $ventaTarjeta->setFecha(new \DateTime);
                $ventaTarjeta->setPagado($valorRestado);
                $ventaTarjeta->setIva($this->impuesto);
                $ventaTarjeta->setImporteIva(number_format($cantidadImpuesto, 2));
                $ventaTarjeta->setPago('Tarjeta');
                $ventaTarjeta->setNumMesa($num_mesa);
                $ventaTarjeta->setComesales(intval($request->request->get('comensales')));
                $ventaTarjeta->setPedidosRef($pedidosRef);
                $ventaTarjeta->setObservaciones($observaciones);
            }

            $this->entityManager->persist($ventaTarjeta);
        }



        $opentickets = $this->entityManager->getRepository(Tickets::class)->findBy(['mesaid' => $mesa->getId()]);

        foreach ($opentickets as $ticket) {
            $this->entityManager->remove($ticket);
            $this->entityManager->flush();
        }

        $mesa->setPorPagar(sprintf("%.2f", 0));
        $mesa->setPagado(sprintf("%.2f", 0));
        $mesa->setFactura(null);
        $mesa->setComensales(0);



        $this->entityManager->persist($mesa);
        $this->entityManager->flush();

        $arrayComensales = $this->mesasRepository->mesasConComensales();

        $totalPagarZonas = $this->mesasRepository->totalZonas($idzona);

        return new JsonResponse([
            'pagado' => 'La mesa ' . $num_mesa . ' se ha cobrado correctamente',
            'ticket_content' =>  $content,
            'total' => $mesa_total,
            'img' => $imagenUrl,
            'arraycomensales' => $arrayComensales,
            'total_zona' => $totalPagarZonas,

        ]);
    }

    /**
     * @Route("/sacartiquet", name="sacar_tiquet")
     */
    public function sacarTiquet(Request $request, EntityManagerInterface $entityManager)
    {



        $repoMesas = $this->entityManager->getRepository(Mesas::class); //New version to get Doctrine in Symfony 5
        $repoPedidos = $this->entityManager->getRepository(Pedidos::class); //New version to get Doctrine in Symfony 5

        $num_mesa = $request->request->get('num_mesa');
        $comensales = $request->request->get('comensales');

        $mesa = $repoMesas->findOneBy(['numero' => $num_mesa]);

        if ($request->request->get('pago') === 'parcial') {
            $repoTickets = $this->entityManager->getRepository(Tickets::class);
            $ticket = $repoTickets->findOneBy(['mesaid' => $mesa->getId()], ['id' => 'DESC']);
            $array = $request->request->get('elementos');


            $pedidoDatosArray = [];
            foreach ($array as $item) {
                $pedidoDatosArray[] = $item["pedidoDatos"];
            }

            if ($ticket === null) {
                $ticket = new Tickets();
                $ticket->setMesaid($mesa);
                $ticket->setNumeroticket(1);
                $ticket->setPedidos($pedidoDatosArray);
                $this->entityManager->persist($ticket);
            } else {
                $ticket2 = new Tickets();
                $ticket2->setMesaid($mesa);
                $ticket2->setNumeroticket($ticket->getNumeroticket() + 1);
                $ticket2->setPedidos($pedidoDatosArray);
                $this->entityManager->persist($ticket2);
            }

            $a_pagar = 0;
            $list_ticket = array();
            foreach ($array as $pedido) {

                $comida = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $pedido["itemId"]]);
                $comidaTienda = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $pedido["itemId"]]);
                $p = $this->entityManager->getRepository(Pedidos::class)->findOneBy(['id' => $pedido["pedidoDatos"]]);

                $precioExtra = 0;
                $extrasString = $p->getComida() ? $p->getComida()->getName() : $p->getProducttienda()->getNombre();
                if ($p->getExtras()) {
                    $extrasString .= ' ->Extras: ';
                    $arrayExtras = $p->getExtras();
                    foreach ($arrayExtras as $extra) {

                        $repoComida = $this->entityManager->getRepository(Comida::class);
                        $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                        $precioExtra += floatval($comidaExtra->getPrecio());
                        $extrasString .= $comidaExtra->getName() . ', ';
                    }
                    $extrasString = rtrim($extrasString, ', ');
                }

                $precio = $precioExtra;

                if ($p->getDescuento()) {

                    $descuento = floatval($p->getDescuento()) / 100;
                    $descuentoEur = $comida->getPrecio() ?  $comida->getPrecio() * $descuento : $comidaTienda->getPvp();
                    $precio += $comida->getPrecio() ? $comida->getPrecio() - $descuentoEur : $comidaTienda->getPvp() - $descuentoEur;
                } else if ($p->getDescuentoEur()) {
                    $precio += $comida->getPrecio() ? $comida->getPrecio() - $p->getDescuentoEur() : $comidaTienda->getPvp() - $p->getDescuentoEur();
                } else if ($p->isInvitacion()) {

                    $precio += 0;
                } else {
                    $precio += $comida->getPrecio() ? $comida->getPrecio() : $comidaTienda->getPvp();
                }


                // Verificar si la comida ya está en el array
                $found = false;
                foreach ($list_ticket as &$item) {
                    if ($item['id'] === $comida->getId()) {
                        $item['cantidad'] += 1;
                        $item['price'] += $precio;
                        $found = true;

                        break;
                    }
                }

                if (!$found) {
                    $temp = array(
                        'id' => $comida->getId(),
                        'name' => $this->quitarAcentos($comida->getName()),
                        'price' => $precio,
                        'cantidad' => 1,
                    );
                    array_push($list_ticket, $temp);
                }

                $a_pagar += $precio;
            }

            $ticketController = new TicketController($entityManager);
            $order = [
                'id' => 2,
                'date' => date("d/m/Y H:i:s"),
                'items' => $list_ticket,
                'totalAmount' => $a_pagar,
                'nummesa' => $mesa->getNumero(),
            ];

            $content = $ticketController->generateTicket($order);
        } else {

            $mesa->setFactura(true);
            $mesa_total = floatval($mesa->getPorPagar());

            $pedidos = $repoPedidos->findBy(['mesa' => $mesa->getId()]);
            $pago = $request->request->get('metodo_pago');
            $list_ticket = array();
            foreach ($pedidos as $pedido) {
                $comida = null;
                $comidaTienda = null;
                if ($pedido->getComida()) {
                    $comida = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $pedido->getComida()->getId()]);
                } else {
                    $comidaTienda = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $pedido->getProducttienda()->getId()]);
                }


                $precioExtra = 0;
                $extrasString = $pedido->getComida() ? $pedido->getComida()->getName() : $pedido->getProducttienda()->getNombre();
                if ($pedido->getExtras()) {
                    $extrasString .= ' ->Extras: ';
                    $arrayExtras = $pedido->getExtras();
                    foreach ($arrayExtras as $extra) {
                        $repoComida = $this->entityManager->getRepository(Comida::class);
                        $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                        $precioExtra += floatval($comidaExtra->getPrecio());
                        $extrasString .= $comidaExtra->getName() . ', ';
                    }
                    $extrasString = rtrim($extrasString, ', ');
                }

                $precio = $precioExtra;

                if ($pedido->getDescuento() !== null) {

                    $descuento = floatval($pedido->getDescuento()) / 100;
                    $descuentoEur = $comida ?  $comida->getPrecio() * $descuento : $comidaTienda->getPvp() * $descuento;
                    $precio += $comida ?  $comida->getPrecio() - $descuentoEur : $comidaTienda->getPvp() - $descuentoEur;
                } else if ($pedido->getDescuentoEur()) {
                    $precio += $comida ? $comida->getPrecio() - $pedido->getDescuentoEur() : $comidaTienda->getPvp() - $pedido->getDescuentoEur();
                } else if ($pedido->isInvitacion()) {

                    $precio += 0;
                } else {
                    $precio += $comida ? $comida->getPrecio() : $comidaTienda->getPvp();
                }

                // Verificar si la comida ya está en el array
                $found = false;
                foreach ($list_ticket as &$item) {
                    if ($item['id'] === ($comida ? $comida->getId() : ($comidaTienda ? $comidaTienda->getId() : null))) {
                        $item['cantidad'] += 1;
                        $item['price'] += $precio;
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    $temp = array(
                        'id' => $comida ? $comida->getId() : $comidaTienda->getId(),
                        'name' => $comida ? $comida->getName() : $comidaTienda->getNombre(),
                        'price' => $precio,
                        'cantidad' => 1,
                    );
                    array_push($list_ticket, $temp);
                }
            }

            $ticketController = new TicketController($this->entityManager);
            $order = [
                'id' => 2,
                'date' => date("d/m/Y H:i:s"),
                'items' => $list_ticket,
                'totalAmount' => $mesa_total,
                'nummesa' => $mesa->getNumero(),
            ];

            $content = $ticketController->generateTicket($order);
        }

        $this->entityManager->flush();


        if (isset($ticket) || isset($ticket2)) {
            return new JsonResponse([
                'ticket_content' =>  $content,
                'numeroTicket' => isset($ticket2) ? $ticket2->getNumeroticket() : $ticket->getNumeroticket(),
                'apagar' => isset($a_pagar) ? $a_pagar : $mesa_total,
                'comensales' => $comensales,
                'content' => $content,



            ]);
        } else {
            return new JsonResponse([
                'ticket_content' =>  $content,
                'apagar' => isset($a_pagar) ? $a_pagar : $mesa_total,
                'comensales' => $comensales,
                'content' => $content,


            ]);
        }
    }

    /**
     * @Route("/unirmesas", name="unir_mesas")
     */
    public function unirMesas(Request $request)
    {


        $union[] = $request->request->get('union');
        $union_nueva[] = $union;
        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesa = $repoMesas->findOneBy(['numero' => $union[0]['select1']]);
        $porPagar = $mesa->getPorPagar();
        unset($union[0]['select1']);
        $mesasSecundariasIds = $union[0]['select2'];

        foreach ($mesasSecundariasIds as $mesaSecundariaId) {

            $mesaSecundaria = $this->entityManager->getRepository(Mesas::class)->findOneBy(['numero' => $mesaSecundariaId]);
            $porPagar += $mesaSecundaria->getPorPagar();
            $mesa->setPorPagar($porPagar);
            $mesaSecundaria->setPorPagar(0);

            $pedidosMesaSecundaria = $mesaSecundaria->getPedidos();

            if ($pedidosMesaSecundaria !== null) {
                foreach ($pedidosMesaSecundaria as $pedido) {
                    $pedido->setMesa($mesa);
                }
            }
        }

        if (is_array($union)) {
            $mesa->setUnionMesas($union[0]['select2']);
        } else {
            $union = gettype($union);
        }

        $this->entityManager->persist($mesa);
        $this->entityManager->flush();

        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesas_libres = $repoMesas->mesasDisponibles();

        $mesas_con_union = $repoMesas->mesasConUniones(1);
        $result_mesas = array();
        foreach ($mesas_con_union as $key => $value) {
            $result[] = "$key";
            $numbers = explode(",", $value);
            $result_mesas = array_merge($result, $numbers);
        }

        return new JsonResponse([
            'union' => $union,
            'mesas_libres' => $mesas_libres,
            'nueva_union' => $union_nueva,
            'total_uniones' => $mesas_con_union,

        ]);
    }

    /**
     * @Route("/separarmesa", name="separar_mesa")
     */
    public function eliminarUnion(Request $request)
    {

        $desunir = $request->request->get('idMesa');

        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesa = $repoMesas->findOneBy(['numero' => $desunir]);
        $idMesa = $repoMesas->obtenerMesaConUnionesPorId($desunir);




        if (empty($mesa->getPedidos())) {
            return new JsonResponse([
                'tiene_uniones' => 'No puedes eliminar una union teniendo pedidos sin cobrar',
                'pedido' => $mesa->getPedidos()

            ], 200);
        }

        if ($mesa->setUnionMesas(null)) {
            $this->entityManager->persist($mesa);
            $this->entityManager->flush();
            $mesas_con_union = $repoMesas->mesasConUniones(1);
            $result_mesas = array();
            foreach ($mesas_con_union as $key => $value) {
                $result[] = "$key";
                $numbers = explode(",", $value);
                $result_mesas = array_merge($result, $numbers);
            }
            return new JsonResponse([
                'desunida' => 'Las mesas se han desunido correctamente',
                'desunion' => $idMesa,
                'total_uniones' => $mesas_con_union,

            ], 200);
        } else {
            return new JsonResponse([
                'desunida' => 'No se ha desunido la mesa',

            ], 400);
        }
    }

    /**
     * @Route("/unionescomedor", name="uniones_comedor")
     */
    function mesaConUnionesComedor(Request $request)
    {
        $idZona = intval($request->request->get('idzona'));
        $zonaname = $this->entityManager->getRepository(Zonas::class)->findOneBy(['id' => $idZona])->getName();
        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesas_con_union = $repoMesas->mesasConUniones($idZona);
        $mesasId = $repoMesas->idMesas($idZona);

        $totalPagarZona = $this->mesasRepository->totalZonas($idZona);


        $result_mesas = array();
        foreach ($mesas_con_union as $key => $value) {
            $numbers = explode(",", $value);
            $result_mesas[$key] = implode(",", $numbers);
        }

        $repoZonas = $this->entityManager->getRepository(Zonas::class);

        $zonaBarra = $repoZonas->findOneBy(['id' => 3]);
        $zonaTerraza = $repoZonas->findOneBy(['id' => 2]);

        return new JsonResponse([
            'uniones_comedor' => $result_mesas,
            // 'barra' => $zonaBarra->isActive(),
            'terraza' => $zonaTerraza->isActive(),
            'mesas' => $mesasId,
            'zonaname' => $zonaname,
            'total_zona' => $totalPagarZona,



        ], 200);
    }
    /**
     * @Route("/unionesterraza", name="uniones_terraza")
     */
    function mesaConUnionesTerraza()
    {
        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesas_con_union = $repoMesas->mesasConUnionesTerraza();

        $result_mesas = array();
        foreach ($mesas_con_union as $key => $value) {
            $numbers = explode(",", $value);
            $result_mesas[$key] = implode(",", $numbers);
        }

        $repoZonas = $this->entityManager->getRepository(Zonas::class);

        $zonaBarra = $repoZonas->findOneBy(['id' => 3]);
        $zonaComedor = $repoZonas->findOneBy(['id' => 1]);

        return new JsonResponse([
            'uniones_terraza' => $result_mesas,
            'barra' => $zonaBarra->isActive(),
            'comedor' => $zonaComedor->isActive(),


        ], 200);
    }


    private function quitarAcentos($cadena)
    {
        $acentos = array(
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'à' => 'a',
            'è' => 'e',
            'ì' => 'i',
            'ò' => 'o',
            'ù' => 'u',
            'ü' => 'u',
            'ñ' => 'n',
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'À' => 'A',
            'È' => 'E',
            'Ì' => 'I',
            'Ò' => 'O',
            'Ù' => 'U',
            'Ü' => 'U',
            'Ñ' => 'N'
        );

        return strtr($cadena, $acentos);
    }

    /**
     * @Route("/eliminarcomida", name="eliminar_comida")
     */
    function eliminarComida(Request $request)
    {
        $mesa_id = $request->request->get('mesa');
        $comidas = $request->request->get('elementosSeleccionados');
        $idzona = intval($request->request->get('idzona'));


        $pedidosRepo =  $this->entityManager->getRepository(Pedidos::class);
        $mesasRepo =  $this->entityManager->getRepository(Mesas::class);
        $comidaRepo = $this->entityManager->getRepository(Comida::class);
        $eliminacionesExitosas = true;

        $mesa = $mesasRepo->findOneBy(['numero' => $mesa_id]);

        $descuento = 0;

        foreach ($comidas as $comidaId) {

            $descuentoEnProducto = 0;
            $precioFinal = 0;
            $comida_tienda = false;
            $pedido = $pedidosRepo->findOneBy([
                'mesa' => $mesa->getId(),
                'comida' => $comidaId,
            ]);

            if ($pedido === null) {
                $comida_tienda = true;
                $pedido = $pedidosRepo->findOneBy([
                    'mesa' => $mesa->getId(),
                    'producttienda' => $comidaId,
                ]);
            }

            if (!$comida_tienda) {
                $precio_comida = $comidaRepo->findOneBy(['id' => $comidaId]);
            } else {
                $precio_tienda = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $comidaId]);
            }

            if ($pedido->getDescuento() !== null) {
                $precioComida = isset($precio_comida) ? $precio_comida->getPrecio() : $precio_tienda->getPvp();
                $descuentoEnProducto = $pedido->getDescuento();

                // Calcular el descuento en valor monetario
                $valorDescuento = ($precioComida * $descuentoEnProducto) / 100;

                // Calcular el precio final después del descuento
                $precioFinal = $precioComida - $valorDescuento;

                // Opcional: formatear el resultado a dos decimales
                $precioFinal = floatval($precioFinal);
            } else if ($pedido->getDescuentoEur() !== null) {
                $precioFinal = $pedido->getDescuentoEur();
            } else if ($pedido->isInvitacion()) {

                $precioFinal = isset($precio_comida) ? floatval($precio_comida->getPrecio()) : floatval($precio_tienda->getPvp());
            }



            $descuento += isset($precio_comida) ? ($precio_comida->getPrecio() - $precioFinal) : ($precio_tienda->getPvp() - $precioFinal);

            if ($pedido) {
                $this->entityManager->remove($pedido);
                $this->entityManager->flush();
            } else {
                $eliminacionesExitosas = false;
            }
        }

        $mesa->setPorPagar($mesa->getPorPagar() - $descuento);
        $this->entityManager->persist($mesa);
        $this->entityManager->flush();


        $icon = '';
        $arrayComensales = [];
        if ($mesa->getPorPagar() === '0') {
            $mesa->setComensales(0);
            $mesa->setPagado(0);
            $icon = $mesa->getIcon();
            $this->entityManager->persist($mesa);
            $this->entityManager->flush();
            $arrayComensales = $this->mesasRepository->mesasConComensales();
        }

        $totalPagarZona = $this->mesasRepository->totalZonas($idzona);





        if ($eliminacionesExitosas) {
            // Todas las eliminaciones fueron exitosas, devolver código de estado 200
            return new JsonResponse([
                'total' => number_format(floatval($mesa->getPorPagar()), 2, ',', '.'),
                'icon' => $icon,
                'arraycomensales' => $arrayComensales,
                'total_zona' => $totalPagarZona,

            ], 200);
        } else {
            // Hubo al menos una eliminación que no fue exitosa, devolver código de estado de error (por ejemplo, 400)
            return new JsonResponse(['mensaje' => 'Al menos una eliminación falló'], 400);
        }
    }


    /**
     * @Route("/enviococina", name="envio_cocina")
     */
    function envioCocina(Request $request)
    {
        $pedidosRepo =  $this->entityManager->getRepository(Pedidos::class);
        $mesa_id = $request->request->get('mesa_id');

        $mesasRepo = $this->entityManager->getRepository(Mesas::class);
        $mesa = $mesasRepo->findOneBy(['numero' => $mesa_id]);

        $pedidos = $pedidosRepo->findBy([
            'mesa' => $mesa->getId(),
            'marchando' => 0,
        ]);

        if (empty($pedidos)) {

            return new JsonResponse([
                'todo_marchando' => 'En esta mesa no hay pedidos para marchar',

            ]);
        } else {
            $order_ticket = array();
            $order_ticket_barra = array();

            foreach ($pedidos as $pedido) {
                if (($pedido->getComida() !== null && $pedido->getComida()->isIsComida() === true)) {
                    $arrayExtras = [];
                    if ($pedido->getExtras() !== null) {
                        foreach ($pedido->getExtras() as $extra) {
                            $comidaExtra = $this->entityManager->getRepository(Comida::class)->findOneBy(['id' => $extra]);
                            $productoExtra = [
                                "nombre" => $comidaExtra->getName(),
                            ];
                            array_push($arrayExtras, $productoExtra);
                        }
                    }

                    $found = false;

                    // Recorrer el array para buscar si el id ya existe
                    foreach ($order_ticket as &$item) {
                        if ($item['id'] == $pedido->getComida()->getId() && $item['numplato'] === $pedido->getNumPlato()) {
                            $item['cantidad'] += 1;
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $temp = array(
                            'id' => $pedido->getComida()->getId(),
                            'name' => $this->quitarAcentos($pedido->getComida()->getName()),
                            'comentario' => $pedido->getComentarios(),
                            'cantidad' => 1,
                            'numplato' => $pedido->getNumPlato(),
                            'extras' => $arrayExtras,
                        );
                        array_push($order_ticket, $temp);
                    }
                } else if (($pedido->getComida() !== null && $pedido->getComida()->isIsbebida() === true) || $pedido->getProducttienda()) {
                    $id = $pedido->getComida() ? $pedido->getComida()->getId() : $pedido->getProducttienda()->getId();
                    $name = $pedido->getComida() ? $pedido->getComida()->getName() : $pedido->getProducttienda()->getNombre();

                    $found = false;

                    // Recorrer el array para buscar si el id ya existe
                    foreach ($order_ticket_barra as &$item) {
                        if ($item['id'] == $id) {
                            $item['cantidad'] += 1;
                            $found = true;
                            break;
                        }
                    }

                    // Si no se encontró, agregar un nuevo elemento
                    if (!$found) {
                        $temp = array(
                            'id' => $id,
                            'name' => $name,
                            'cantidad' => 1,
                        );
                        array_push($order_ticket_barra, $temp);
                    }
                }

                $pedido->setMarchando(1);
                $this->entityManager->persist($pedido);
                $this->entityManager->flush();
            }


            $ticketController = new TicketController($this->entityManager);
            $order = [

                'items' => $order_ticket,
                'nummesa' => $mesa_id
            ];

            $order_barra = [

                'items' => $order_ticket_barra,
                'nummesa' => $mesa_id
            ];


            $content = $ticketController->generateTicketKitchen($order);
            $content_barra = $ticketController->generateTicketBarra($order_barra);


            return new JsonResponse([
                'cocina' => 'Oído cocina, marchando la mesa: ' . $mesa_id,
                'ticket_content' =>  $content,
                'ticket_content_barra' =>  $content_barra,
                'mesa' => $mesa->getNumero(),

            ]);
        }
    }
    /**
     * @Route("/eliminarproducto", name="eliminar_producto")
     */
    function añadirProducto(Request $request)
    {

        $pedido = $request->request->get('pedido');
        $mesa = $request->request->get('mesa');
        $idzona = intval($request->request->get('idzona'));


        $repoPedidos = $this->entityManager->getRepository(Pedidos::class);
        $repoMesas = $this->entityManager->getRepository(Mesas::class);

        $pedido = $repoPedidos->findOneBy(['id' => $pedido]);
        $mesa = $repoMesas->findOneBy(['numero' => $mesa]);

        $restar = $pedido->getComida() ? $pedido->getComida()->getPrecio() : $pedido->getProducttienda()->getPvp();

        $totalapagar = $mesa->getPorPagar();
        $mesa->setPorPagar($totalapagar - $restar);

        $this->entityManager->persist($mesa);
        $this->entityManager->remove($pedido);
        $this->entityManager->flush();


        if ($mesa->getPedidos()->isEmpty()) {


            $mesa->setComensales(0);
            $this->entityManager->persist($mesa);
            $this->entityManager->flush();

            $arrayComensales = $this->mesasRepository->mesasConComensales();

            $imgurl = $mesa->getIcon();
        }

        $totalPagarZona = $this->mesasRepository->totalZonas($idzona);

        return new JsonResponse([
            'restar' => $restar,
            'arraycomensales' => isset($arrayComensales) ? $arrayComensales : '',
            'icon' => isset($imgurl) ? $imgurl : '',
            'total_zona' => $totalPagarZona,

        ]);
    }


    /**
     * @Route("/invitarproducto", name="invitar")
     */
    function invitacion(Request $request)
    {

        $pedido = $request->request->get('pedido');
        $mesa2 = $request->request->get('mesa');
        $ref = $request->request->get('ref');
        $idzona = $request->request->get('idzona');


        $repoPedidos = $this->entityManager->getRepository(Pedidos::class);
        $repoMesas = $this->entityManager->getRepository(Mesas::class);

        $pedido = $repoPedidos->findOneBy(['id' => $pedido]);
        $mesa = $repoMesas->findOneBy(['numero' => $mesa2]);

        $precioproducto = null;

        if ($ref === '1') {
            $pedido->setInvitacion(true);
            $mesa->setPorPagar($mesa->getPorPagar() - ($pedido->getComida() ? $pedido->getComida()->getPrecio() : $pedido->getProducttienda()->getPvp()));
        } else {
            $pedido->setInvitacion(false);
            $mesa->setPorPagar($mesa->getPorPagar() + ($pedido->getComida() ? $pedido->getComida()->getPrecio() : $pedido->getProducttienda()->getPvp()));
            $precioproducto = $pedido->getComida() ? $pedido->getComida()->getPrecio() : $pedido->getProducttienda()->getPvp();
        }

        $this->entityManager->persist($pedido);
        $this->entityManager->persist($mesa);
        $this->entityManager->flush();

        $datos_mesa = $mesa->getPedidos();

        $control_mesa = null;
        $totalMesa = 0;

        $data_mesa = array();
        foreach ($datos_mesa as $p_mesa) {
            $precioExtra = 0;
            $extrasString = $p_mesa->getComida() ? $p_mesa->getComida()->getName() : $p_mesa->getProducttienda()->getNombre();
            if ($p_mesa->getExtras()) {
                $extrasString .= ' ->Extras: ';
                $arrayExtras = $p_mesa->getExtras();
                foreach ($arrayExtras as $extra) {
                    $repoComida = $this->entityManager->getRepository(Comida::class);
                    $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                    $precioExtra += floatval($comidaExtra->getPrecio());
                    $extrasString .= $comidaExtra->getName() . ', ';
                }
                $extrasString = rtrim($extrasString, ', ');
            }
            $temp = array(
                'comida' => $extrasString,
                'id' => $p_mesa->getComida() ? $p_mesa->getComida()->getId() : $p_mesa->getProducttienda()->getId(),
                'precio' => $p_mesa->isInvitacion() ? 0 : ($p_mesa->getComida() ? ($p_mesa->getComida()->getPrecio() + $precioExtra) : ($p_mesa->getProducttienda()->getPvp() + $precioExtra)),
                'marchando' => $p_mesa->isMarchando(),
                'pedido_mesa' => $p_mesa->getId(),
                'invitacion' => $p_mesa->isInvitacion() ? 1 : 0,

            );
            array_push($data_mesa, $temp);

            if (!$p_mesa->isInvitacion()) {
                $totalMesa +=  floatval($p_mesa->getComida() ? $p_mesa->getComida()->getPrecio() : $p_mesa->getProducttienda()->getPvp());
            }
        }

        $pedidosRepo =  $this->entityManager->getRepository(Pedidos::class);
        $mesa_id = $request->request->get('mesa_id');

        if (!empty($data_mesa)) {
            $pedidos = $pedidosRepo->findBy([
                'mesa' => $mesa_id,
                'marchando' => 0
            ]);

            $control_mesa = (empty($pedidos)) ? 1 : 0;
        }

        $totalPagarZona = $this->mesasRepository->totalZonas($idzona);

        return new JsonResponse([
            'pedido_mesa' => $data_mesa,
            'total_mesa' => $mesa->getPorPagar(),
            'control_mesa' => $control_mesa,
            'precioproducto' => $precioproducto,
            'total_zona' => $totalPagarZona,

        ]);
    }
    /**
     * @Route("/descuento", name="descuento")
     */
    function descuento(Request $request)
    {

        $mesa_id = $request->request->get('mesa');
        $idzona = $request->request->get('idzona');
        $descuento = number_format(floatval($request->request->get('descuento')), 2);
        $pedido_id = $request->request->get('pedido');
        $tipo = $request->request->get('tipo');

        $repoPedidos = $this->entityManager->getRepository(Pedidos::class);
        $pedido = $repoPedidos->findOneBy(["id" => $pedido_id]);
        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesa = $repoMesas->findOneBy(["numero" => $mesa_id]);

        $precioFinal = 0;

        if ($tipo === "porcentaje") {
            $pedido->setDescuento(floatval($descuento));
            $descuentoPorcentaje = floatval($descuento) / 100; // Convertir el porcentaje a fracción
            $descuentoEuros = $pedido->getComida() ? ($pedido->getComida()->getPrecio() * $descuentoPorcentaje) : ($pedido->getProducttienda()->getPvp() * $descuentoPorcentaje);
            $mesa->setPorPagar($mesa->getPorPagar() - $descuentoEuros);
            $precioFinal = $pedido->getComida() ? ($pedido->getComida()->getPrecio() - $descuentoEuros) : ($pedido->getProducttienda()->getPvp() - $descuentoEuros);
        } else {
            $pedido->setDescuentoEur(floatval($descuento));
            $mesa->setPorPagar($mesa->getPorPagar() - $descuento);
            $precioFinal = $pedido->getComida() ? ($pedido->getComida()->getPrecio() - $descuento) : ($pedido->getProducttienda()->getPvp() - $descuento);
        }

        // die;

        $this->entityManager->persist($pedido);
        $this->entityManager->persist($mesa);
        $this->entityManager->flush();

        $totalPagarZona = $this->mesasRepository->totalZonas($idzona);

        return new JsonResponse([
            "total" => number_format($mesa->getPorPagar(), 2),
            "preciofinal" => number_format($precioFinal, 2),
            "total_zona" => $totalPagarZona,

        ]);
    }

    /**
     * @Route("/descuentomesa", name="descuento_mesa")
     */
    function descuentoMesa(Request $request)
    {

        $mesa_id = $request->request->get('mesa');
        $descuento = number_format(floatval($request->request->get('descuento')), 2);
        $tipo = $request->request->get('tipo');

        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesa = $repoMesas->findOneBy(["numero" => $mesa_id]);

        $totalMesa = 0;

        foreach ($mesa->getPedidos() as $pedido) {
            if ($pedido->getComida()) {
                $totalMesa += $pedido->getComida()->getPrecio();
                if ($tipo === "porcentaje") {
                    $pedido->setDescuento(floatval($descuento));
                } else {
                    $pedido->setDescuentoEur(floatval($descuento));
                }
            } else {
                $totalMesa += $pedido->getProducttienda()->getPvp();
                if ($tipo === "porcentaje") {
                    $pedido->setDescuento(floatval($descuento));
                } else {
                    $pedido->setDescuentoEur(floatval($descuento));
                }
            }

            $this->entityManager->persist($pedido);
            $this->entityManager->flush();
        }

        if ($tipo === "porcentaje") {
            $descuentoPorcentaje = floatval($descuento) / 100; // Convertir el porcentaje a fracción
            $descuentoEuros = $totalMesa * $descuentoPorcentaje;
            $mesa->setPorPagar($totalMesa - $descuentoEuros);
        } else {
            $mesa->setPorPagar($totalMesa - $descuento);
        }
        $this->entityManager->persist($mesa);
        $this->entityManager->flush();

        return new JsonResponse([
            "total" => $mesa->getPorPagar(),

        ]);
    }

    /**
     * @Route("/cambioefectivo", name="cambio_efectivo")
     */
    function cambioEfectivo(Request $request)
    {
        $fechaActual = new \DateTime();
        $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));
        $objetoDiaActual = $this->entityManager->getRepository(Cajaregistro::class)->findOneBy(['dia' => $fecha_sin_hora]);

        if ($request->request->has('total')) {
            $total = $request->request->get('total');

            if ($objetoDiaActual === null) {
                return new JsonResponse([
                    "message" => 'No has iniciado la caja del dia de hoy',

                ]);
            }

            if ($objetoDiaActual->getTotalCaja() === null) {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getInicioCaja() + floatval($total));
            } else {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getTotalCaja() + floatval($total));
            }



            $this->entityManager->persist($objetoDiaActual);
            $this->entityManager->flush();

            return new JsonResponse([]);
        } else {
            $repoMesas = $this->entityManager->getRepository(Mesas::class); //New version to get Doctrine in Symfony 5
            $mesa = $repoMesas->findOneBy(['numero' => $request->request->get('num_mesa')]);

            if ($objetoDiaActual === null) {
                return new JsonResponse([
                    "message" => 'No has iniciado la caja del dia de hoy',

                ]);
            }

            if ($objetoDiaActual->getTotalCaja() === null) {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getInicioCaja() + floatval($mesa->getPorPagar()));
            } else {

                $objetoDiaActual->setTotalCaja($objetoDiaActual->getTotalCaja() + floatval($mesa->getPorPagar()));
            }



            $this->entityManager->persist($objetoDiaActual);
            $this->entityManager->flush();

            return new JsonResponse([
                "total" => number_format($mesa->getPorPagar(), 2),

            ]);
        }
    }
    /**
     * @Route("/cambioefectivoparcial", name="cambio_efectivo_parcial")
     */
    function cambioEfectivoParcial(Request $request)
    {


        $em = $this->getDoctrine()->getManager(); //DEPRECATED
        // $myRepository = $this->$entityManager->getRepository(Mesas::class); //New version to get Doctrine in Symfony 5
        $num_mesa = $request->request->get('num_mesa');
        $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $num_mesa]);
        $mesa_total = floatval($mesa->getPorPagar());

        $pedidos = $request->request->get('elementos');

        $a_pagar = 0;
        $list_ticket = array();
        foreach ($pedidos as $pedido) {

            $comida = $em->getRepository(Comida::class)->findOneBy(['id' => $pedido["itemId"]]);
            $p = $this->entityManager->getRepository(Pedidos::class)->findOneBy(['id' => $pedido["pedidoDatos"]]);

            $precioExtra = 0;
            $extrasString = $p->getComida()->getName();
            if ($p->getExtras()) {
                $extrasString .= ' ->Extras: ';
                $arrayExtras = $p->getExtras();
                foreach ($arrayExtras as $extra) {

                    $repoComida = $this->entityManager->getRepository(Comida::class);
                    $comidaExtra = $repoComida->findOneBy(['id' => $extra]);
                    $precioExtra += floatval($comidaExtra->getPrecio());
                    $extrasString .= $comidaExtra->getName() . ', ';
                }
                $extrasString = rtrim($extrasString, ', ');
            }

            $precio = $precioExtra;

            if ($p->getDescuento()) {
                $descuento = floatval($p->getDescuento()) / 100;
                $descuentoEur = $comida->getPrecio() * $descuento;
                $precio += $comida->getPrecio() - $descuentoEur;
            } else if ($p->getDescuentoEur()) {
                $precio += $comida->getPrecio() - $p->getDescuentoEur();
            } else {
                $precio += $comida->getPrecio();
            }
            $a_pagar += $precio;
        }

        return new JsonResponse([
            "total" => $a_pagar,

        ]);
    }

    /**
     * @Route("/imprimirticket", name="imprimir_ticket")
     */
    function imprimirTicket(Request $request)
    {
        $a_pagar = floatval($request->request->get('apagar'));
        $content = $request->request->get('content');
        $num_mesa = $request->request->get('num_mesa');
        $comensales = intval($request->request->get('comensales'));

        $currentDateTime = date('Y-m-d H:i:s');
        $token = $this->security->getToken();

        // Verificar si hay un token y si el usuario está autenticado
        if ($token && $token->isAuthenticated()) {
            // Obtener el objeto de usuario actual
            $user = $token->getUser();

            // Verificar si el usuario es un objeto de usuario (puede ser una cadena en algunos casos)
            if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                // Obtener el nombre de usuario
                $username = $user->getUserIdentifier();
            }
        }

        $impresora = $this->entityManager->getRepository(Impresoras::class)->findOneBy(['id' =>  1]);
        $info = $this->entityManager->getRepository(Info::class)->findOneBy(['id' =>  1]);
        $printer = new SunmiCloudPrinter(384);
        // Encabezado del ticket
        $printer->lineFeed();
        $img = 'public/' . $info->getLogo();
        // $printer->appendImage($img, 0);
        // $printer->setLineSpacing(80);
        $printer->setPrintModes(true, true, false);
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
        $printer->appendText($info->getName());
        $printer->setPrintModes(false, false, false);
        $printer->lineFeed();
        $printer->appendText('Direccion: ' . $info->getDir());
        $printer->lineFeed();
        $printer->appendText('Telefono: ' . $info->getTelf());
        $printer->lineFeed();
        $printer->appendText('Email: ' . $info->getEmail());
        $printer->lineFeed();
        $printer->appendText('CIF: ' . $info->getCif());
        $printer->lineFeed();
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
        $printer->appendText('---------------------------------------------');
        $printer->lineFeed();
        $printer->appendText('Fecha: ' . $currentDateTime);
        $printer->lineFeed();
        $printer->appendText('Mesa: ' . $num_mesa);
        $printer->lineFeed();
        $printer->appendText('Comensales: ' . $comensales);
        $printer->lineFeed();
        $printer->appendText('Atendido por: ' . $username);
        $printer->lineFeed();
        $printer->appendText('------------------------------------------------');
        $printer->lineFeed();
        $printer->appendText('Uds    Descripción                       Total');
        $printer->lineFeed();
        $printer->appendText('------------------------------------------------');
        $printer->lineFeed();
        // Cuerpo del ticket
        $printer->restoreDefaultLineSpacing();
        $printer->setPrintModes(false, false, false);
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

        $printer->setupColumns(
            [96, SunmiCloudPrinter::ALIGN_LEFT, 0],
            [144, SunmiCloudPrinter::ALIGN_CENTER, 0],
            [0, SunmiCloudPrinter::ALIGN_RIGHT, SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]
        );
        $printer->appendText($content);
        $printer->lineFeed();
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_RIGHT);
        $printer->appendText('------------------------');
        $printer->lineFeed();
        $printer->setPrintModes(true, true, false);
        $printer->appendText('EUROS ' . $a_pagar);
        $printer->lineFeed();
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
        $printer->setPrintModes(false, false, false);
        $printer->appendText('------------------------------------------------');
        $printer->lineFeed();
        $printer->setLineSpacing(40);
        $printer->appendText('I.V.A 10% Incluido');
        $printer->lineFeed();

        $totalporpersona = $a_pagar / $comensales;

        $printer->appendText('Precio por persona: ' . number_format(floatval($totalporpersona), 2) . '€');
        $printer->lineFeed();

        $printer->lineFeed(4);
        $printer->cutPaper(true);

        //替换为你设备的SN号
        $sn = $impresora->getSnBarra();
        $printer->pushContent($sn, sprintf("%s_%010d", $sn, time()));





        return new JsonResponse([]);
    }

    /**
     * @Route("/imprimirticketcocina", name="imprimir_ticket_cocina")
     */
    function imprimirTicketCocina(Request $request): Response
    {
        $mesa = $request->request->get('mesa');
        $content = $request->request->get('content');
        $contentbarra = $request->request->get('contentbarra');

        $impresoras = $this->entityManager->getRepository(Impresoras::class)->findOneBy(['id' => 1]);

        if ($content !== '') {

            $printer = new SunmiCloudPrinter(384);
            // Encabezado del ticket Cocina
            $printer->lineFeed();
            $printer->lineFeed();
            $printer->lineFeed();
            $printer->lineFeed();
            $printer->lineFeed();
            $printer->lineFeed();
            // $printer->appendImage($img, 0);
            // $printer->setLineSpacing(80);
            $printer->setPrintModes(true, true, true);
            $printer->setupColumns([0, SunmiCloudPrinter::ALIGN_CENTER, SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]);
            $printer->printInColumns('Mesa: ' . $mesa . "\n");
            $printer->lineFeed();

            // Cuerpo del ticket
            $printer->restoreDefaultLineSpacing();
            $printer->setPrintModes(false, true, true);
            $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

            // $printer->setPrintModes(false, false, true);
            $printer->appendText($content);
            $printer->lineFeed(4);
            $printer->lineFeed();
            $printer->lineFeed();
            $printer->lineFeed();
            $printer->cutPaper(true);

            $sn = $impresoras->getSnCocina();
            $printer->pushContent($sn, sprintf("%s_%010d", $sn, time()));
        }


        if ($contentbarra !== '') {
            // Encabezado del ticket Barra
            $printer = new SunmiCloudPrinter(384);
            // Encabezado del ticket
            $printer->lineFeed();
            // $printer->appendImage($img, 0);
            // $printer->setLineSpacing(80);
            $printer->setPrintModes(true, true, true);
            $printer->setupColumns([0, SunmiCloudPrinter::ALIGN_CENTER, SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]);
            $printer->printInColumns('Mesa: ' . $mesa . "\n");
            $printer->lineFeed();

            // Cuerpo del ticket
            $printer->restoreDefaultLineSpacing();
            $printer->setPrintModes(false, true, true);
            $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

            // $printer->setPrintModes(false, false, true);
            $printer->appendText($contentbarra);
            $printer->lineFeed(4);
            $printer->cutPaper(true);

            $sn = $impresoras->getSnBarra();
            $printer->pushContent($sn, sprintf("%s_%010d", $sn, time()));
        }








        return new JsonResponse([]);
    }

    /**
     * @Route("/savecoordenadas", name="save_coordenadas")
     */
    function guardarCoordenadas(Request $request): Response
    {
        $mesa = $request->request->get('mesaId');
        $x = $request->request->get('x');
        $y = $request->request->get('y');

        $mesa = $this->entityManager->getRepository(Mesas::class)->findOneBy(['numero' => $mesa]);

        $mesa->setCoordX(floatval($x));
        $mesa->setCoordY(floatval($y));

        $this->entityManager->persist($mesa);
        $this->entityManager->flush();






        return new JsonResponse([]);
    }

    /**
     * @Route("/traspasarpedidos", name="traspasar_pedidos")
     */
    function traspasarPedidos(Request $request): Response
    {
        $mesa1 = $request->request->get('mesa1');
        $mesa2 = $request->request->get('mesa2');


        $mesaInicial = $this->entityManager->getRepository(Mesas::class)->findOneBy(['numero' => $mesa1]);
        $mesaObjetivo = $this->entityManager->getRepository(Mesas::class)->findOneBy(['id' => $mesa2]);


        $pedidos = $this->entityManager->getRepository(Pedidos::class)->findBy(['mesa' => $mesaInicial->getId()]);

        if ($pedidos) {
            foreach ($pedidos as $pedido) {
                $pedido->setMesa($mesaObjetivo);
                $this->entityManager->persist($pedido);
            }

            $mesaObjetivo->setPagado($mesaInicial->getPagado());
            $mesaInicial->setPagado(0.00);
            $mesaObjetivo->setComensales($mesaInicial->getComensales());
            $mesaInicial->setComensales(0);
            $mesaObjetivo->setPorPagar($mesaInicial->getPorPagar());
            $mesaInicial->setPorPagar(0.00);

            $this->entityManager->persist($mesaInicial);
            $this->entityManager->persist($mesaObjetivo);
            $this->entityManager->flush();
        } else {

            return new JsonResponse([
                'error' => "No hay pedidos para traspasar."

            ]);
        }

        $arrayComensales = $this->mesasRepository->mesasConComensales();

        return new JsonResponse([
            'success' => "Traspasado correctamente.",
            'icon' => $mesaInicial->getIcon(),
            'mesainicio' => $mesaInicial->getNumero(),
            'mesaobjetivo' => $mesaObjetivo->getNumero(),
            'arraycomensales' => $arrayComensales


        ]);
    }




    /**
     * @Route("/buscar", name="buscar")
     */
    function buscar(Request $request): Response
    {

        $term = $request->request->get('term');

        // Crear una consulta personalizada utilizando el QueryBuilder
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from('App\Entity\Productostienda', 'c')
            ->where('c.nombre LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->getQuery();

        // Ejecutar la consulta y obtener los resultados
        $comidas = $query->getResult();

        // Crear un array de resultados para devolver como JSON
        $results = [];
        foreach ($comidas as $comida) {
            $results[] = [
                'id' => $comida->getId(),
                'nombre' => $comida->getNombre(),
                'precio' => number_format($comida->getPvp(), 2),
                // Agrega más propiedades según sea necesario
            ];
        }

        return new JsonResponse([
            'busqueda' => $results
        ]);
    }


    /**
     * @Route("/ventatienda", name="generarVentaTienda")
     */
    function ventaTienda(Request $request): Response
    {

        $pago = $request->request->get('metodopago');
        $iva = $request->request->get('iva');
        $total = $request->request->get('total');
        $datos = json_decode($request->request->get('datos'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }
        $currentDateTime = date('Y-m-d H:i:s');
        // $fechaActual = new DateTime();
        $fechaActual = new \DateTime();
        $fecha_sin_hora = new \DateTime($fechaActual->format('Y-m-d'));

        $list_ticket = [];
        $array_pedidoref = [];
        // Recorrer el array de datos y obtener los IDs
        foreach ($datos as $item) {
            // Verificar si el item tiene la clave 'id'
            if (isset($item['id'])) {
                $comida = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $item['id']]); // Agregar el ID al array de IDs

                $temp = array(
                    'id' => $comida->getId(),
                    'name' => $comida->getNombre(),
                    'price' => $comida->getPvp() * $item["quantity"],
                    'cantidad' => $item["quantity"],
                );
                array_push($list_ticket, $temp);


                $stockComida = $this->entityManager->getRepository(stock::class)->findOneBy(['producttienda' => $item['id']]);
                $stockComida->setAmount($stockComida->getAmount() - $item['quantity']);
                $this->entityManager->persist($stockComida);
                $this->entityManager->flush();
            }

            $rand = rand(1, 9999);
            $dateRef = date('ymdhms');
            $numRef = 'T' . '-' . $dateRef . $rand;

            array_push($array_pedidoref, $numRef);

            $pedidoHistorial = array(
                'mesa' => 'Tienda',
                'comida' =>  $comida->getNombre(),
                'precio' => $comida->getPvp() * $item["quantity"],
                'precio_total' => $total,
                'num_ref' => $numRef,
                'fecha' => $fechaActual,
                'user' => $this->getUser(),
                'iva' => $iva,
            );
            $newPedidoHistorial = new HistorialPedidos();
            $newPedidoHistorial->setMesa($pedidoHistorial['mesa']);
            $newPedidoHistorial->setComida($pedidoHistorial['comida']);
            $newPedidoHistorial->setPrecio($pedidoHistorial['precio']);
            $newPedidoHistorial->setPrecioTotal($pedidoHistorial['precio_total']);
            $newPedidoHistorial->setFecha($pedidoHistorial['fecha']);
            $newPedidoHistorial->setUser($pedidoHistorial['user']);
            $newPedidoHistorial->setNumRef($pedidoHistorial['num_ref']);
            $newPedidoHistorial->setIva($pedidoHistorial['iva']);


            $this->entityManager->persist($newPedidoHistorial);
            $this->entityManager > flush();
        }

        $pedido_ref = json_encode($array_pedidoref);

        $cantidadImpuesto = ($total * $iva) / 100;
        $valorRestado = $total - $cantidadImpuesto;

        if ($pago === 'efectivo') {

            $venta = new Ventas();
            $venta->setFecha($fechaActual);
            $venta->setPagado($valorRestado);
            $venta->setIva($iva);
            $venta->setImporteIva($cantidadImpuesto);
            $venta->setPedidosRef($pedido_ref);
            $venta->setPago('Efectivo');
            $this->entityManager->persist($venta);
            $this->entityManager->flush();
        } else {

            $venta = new Ventas();
            $venta->setFecha($fechaActual);
            $venta->setPagado($valorRestado);
            $venta->setIva($iva);
            $venta->setImporteIva($cantidadImpuesto);
            $venta->setPedidosRef($pedido_ref);
            $venta->setPago('Tarjeta');
            $this->entityManager->persist($venta);
            $this->entityManager->flush();
        }



        $ticketController = new TicketController($this->entityManager);
        $order = [
            'items' => $list_ticket,
            'totalAmount' => $total,
        ];

        $content = $ticketController->generateTicketTienda($order);


        $token = $this->security->getToken();

        // Verificar si hay un token y si el usuario está autenticado
        if ($token && $token->isAuthenticated()) {
            // Obtener el objeto de usuario actual
            $user = $token->getUser();

            // Verificar si el usuario es un objeto de usuario (puede ser una cadena en algunos casos)
            if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                // Obtener el nombre de usuario
                $username = $user->getUserIdentifier();
            }
        }


        return new JsonResponse([['message' => 'Datos recibidos y procesados correctamente']]);
    }

    /**
     * @Route("/tickettienda", name="generarTicketTienda")
     */
    function ticketTienda(Request $request): Response
    {

        // $pago = $request->request->get('metodopago');
        $iva = $request->request->get('iva');
        $total = $request->request->get('total');
        $datos = json_decode($request->request->get('datos'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON'], 400);
        }

        $list_ticket = [];
        $array_pedidoref = [];
        // Recorrer el array de datos y obtener los IDs
        foreach ($datos as $item) {
            // Verificar si el item tiene la clave 'id'
            if (isset($item['id'])) {
                $comida = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $item['id']]); // Agregar el ID al array de IDs

                $temp = array(
                    'id' => $comida->getId(),
                    'name' => $comida->getNombre(),
                    'price' => $comida->getPvp() * $item["quantity"],
                    'cantidad' => $item["quantity"],
                );
                array_push($list_ticket, $temp);
            }
        }

        $pedido_ref = json_encode($array_pedidoref);

        $cantidadImpuesto = ($total * $iva) / 100;
        $valorRestado = $total - $cantidadImpuesto;

        $ticketController = new TicketController($this->entityManager);
        $order = [
            'items' => $list_ticket,
            'totalAmount' => $total,
        ];

        $content = $ticketController->generateTicketTienda($order);

        $currentDateTime = date('Y-m-d H:i:s');


        $token = $this->security->getToken();

        // Verificar si hay un token y si el usuario está autenticado
        if ($token && $token->isAuthenticated()) {
            // Obtener el objeto de usuario actual
            $user = $token->getUser();

            // Verificar si el usuario es un objeto de usuario (puede ser una cadena en algunos casos)
            if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                // Obtener el nombre de usuario
                $username = $user->getUserIdentifier();
            }
        }

        $impresora = $this->entityManager->getRepository(Impresoras::class)->findOneBy(['id' =>  1]);
        $info = $this->entityManager->getRepository(Info::class)->findOneBy(['id' =>  1]);
        $printer = new SunmiCloudPrinter(384);
        // Encabezado del ticket
        $printer->lineFeed();
        $img = 'public/' . $info->getLogo();
        // $printer->appendImage($img, 0);
        // $printer->setLineSpacing(80);
        $printer->setPrintModes(true, true, false);
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_CENTER);
        $printer->appendText($info->getName());
        $printer->setPrintModes(false, false, false);
        $printer->lineFeed();
        $printer->appendText('Direccion: ' . $info->getDir());
        $printer->lineFeed();
        $printer->appendText('Telefono: ' . $info->getTelf());
        $printer->lineFeed();
        $printer->appendText('Email: ' . $info->getEmail());
        $printer->lineFeed();
        $printer->appendText('CIF: ' . $info->getCif());
        $printer->lineFeed();
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
        $printer->appendText('---------------------------------------------');
        $printer->lineFeed();
        $printer->appendText('Fecha: ' . $currentDateTime);
        $printer->lineFeed();
        $printer->appendText('Atendido por: ' . $username);
        $printer->lineFeed();
        $printer->appendText('------------------------------------------------');
        $printer->lineFeed();
        $printer->appendText('Uds    Descripción                       Total');
        $printer->lineFeed();
        $printer->appendText('------------------------------------------------');
        $printer->lineFeed();
        // Cuerpo del ticket
        $printer->restoreDefaultLineSpacing();
        $printer->setPrintModes(false, false, false);
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

        $printer->setupColumns(
            [96, SunmiCloudPrinter::ALIGN_LEFT, 0],
            [144, SunmiCloudPrinter::ALIGN_CENTER, 0],
            [0, SunmiCloudPrinter::ALIGN_RIGHT, SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]
        );
        $printer->appendText($content);
        $printer->lineFeed();
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_RIGHT);
        $printer->appendText('------------------------');
        $printer->lineFeed();
        $printer->setPrintModes(true, true, false);
        $printer->appendText('Base imponible ' . number_format($valorRestado, 2, '.', ',') . ' €');
        $printer->lineFeed();
        $printer->appendText('IVA ' . $iva . '% ' . number_format($cantidadImpuesto, 2, '.', ','));
        $printer->lineFeed();
        $printer->appendText('TOTAL ' . number_format($total, 2, '.', ',') . ' €');
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);
        $printer->setPrintModes(false, false, false);
        $printer->appendText('------------------------------------------------');
        $printer->lineFeed();
        $printer->setLineSpacing(40);
        $printer->appendText('I.V.A Incluido del ' . $iva . '%');
        $printer->lineFeed();


        $printer->lineFeed(4);
        $printer->cutPaper(true);

        //替换为你设备的SN号
        $sn = $impresora->getSnBarra();
        $printer->pushContent($sn, sprintf("%s_%010d", $sn, time()));

        return new JsonResponse([['message' => 'Datos recibidos y procesados correctamente']]);
    }
    /**
     * @Route("/añadirmesatienda", name="añadir_mesa_tienda")
     */
    function añadirMesaTienda(Request $request): Response
    {

        $data = json_decode($request->getContent(), true); // Decodificar los datos JSON
        $mesa = $this->entityManager->getRepository(Mesas::class)->findOneBy(['numero' => $data['mesa']]); // Agregar el ID al array de IDs
        $totalañadir = 0;
        $list_ticket = [];
        $data_mesa = array();
        // Recorrer el array de datos y obtener los IDs
        foreach ($data['datos'] as $item) {
            // Verificar si el item tiene la clave 'id'
            if (isset($item['id'])) {

                $comida = $this->entityManager->getRepository(Productostienda::class)->findOneBy(['id' => $item['id']]); // Agregar el ID al array de IDs

                for ($i = 0; $i < $item['quantity']; $i++) {
                    $pedido = new Pedidos();
                    $pedido->setMesa($mesa);
                    $pedido->setProducttienda($comida);
                    $pedido->setMarchando(0);

                    $rand = rand(1, 9999);
                    $dateRef = date('ymdhms');
                    $mesaNum = $mesa->getNumero();
                    $numRef = $mesaNum . '-' . $dateRef . $rand;

                    $pedido->setNumRef($numRef);

                    $this->entityManager->persist($pedido);
                    $this->entityManager->flush();

                    $temp = array(
                        'comida' => $comida->getNombre(),
                        'id' => $comida->getId(),
                        'precio' => $comida->getPvp(),
                        'pedido_mesa' => $pedido->getId()

                    );
                    array_push($data_mesa, $temp);
                }

                $totalañadir += $item['quantity'] * $comida->getPvp();
            }
        }



        $mesa->setPorPagar($mesa->getPorPagar() + $totalañadir);
        $this->entityManager->persist($mesa);
        $this->entityManager->flush();


        return new JsonResponse([
            'pedido_mesa' => $data_mesa,
        ]);
    }

    /**
     * @Route("/imprimirplatolisto", name="imprimir_plato_listo")
     */
    function imprimirPlatoListo(Request $request): Response
    {

        $data = json_decode($request->getContent(), true);



        $mesa = $data['mesa'];
        $nombreplato = $data['nombreplato'];
        $cantidad = $data['cantidad'];

        $impresoras = $this->entityManager->getRepository(Impresoras::class)->findOneBy(['id' => 1]);

        $printer = new SunmiCloudPrinter(384);
        // Encabezado del ticket Cocina
        $printer->lineFeed();
        // $printer->appendImage($img, 0);
        // $printer->setLineSpacing(80);
        $printer->setPrintModes(true, true, true);
        $printer->setupColumns([0, SunmiCloudPrinter::ALIGN_CENTER, SunmiCloudPrinter::COLUMN_FLAG_BW_REVERSE]);
        $printer->printInColumns('Mesa: ' . $mesa . "\n");
        $printer->lineFeed();

        // Cuerpo del ticket
        $printer->restoreDefaultLineSpacing();
        $printer->setPrintModes(false, true, true);
        $printer->setAlignment(SunmiCloudPrinter::ALIGN_LEFT);

        // $printer->setPrintModes(false, false, true);
        $printer->appendText($nombreplato . ' x' . $cantidad);
        $printer->lineFeed(4);
        $printer->cutPaper(true);

        $sn = $impresoras->getSnCocina();
        $printer->pushContent($sn, sprintf("%s_%010d", $sn, time()));






        return new JsonResponse([]);
    }
}
