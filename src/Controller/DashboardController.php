<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use App\Entity\TipoComida;
use App\Entity\Mesas;
use App\Entity\Pedidos;
use App\Entity\Comida;
use App\Entity\Zonas;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personalizacion;
use App\Entity\Statscomensales;
use App\Entity\Stock;
use Symfony\Component\Security\Core\Security;

use function PHPUnit\Framework\isNull;

class DashboardController extends AbstractController
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;

        $this->security = $security;
    }
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $repoTipo = $this->entityManager->getRepository(TipoComida::class);
        $TiposComida = $repoTipo->findBy(['active' => true]);

        //List para movil
        $newTiposMovil = array();

        foreach ($TiposComida as $tipocomida) {
            $temp = array(
                'id' => $tipocomida->getId(),
                'name' => $tipocomida->getName(),
                'icon' => $tipocomida->getIcon() ?? $tipocomida->getRutaImg()

            );
            array_push($newTiposMovil, $temp);
        }

        //List para Desktop
        $newTipos = array();
        foreach ($TiposComida as $tipocomida) {
            var_dump($tipocomida->getIcon());
            var_dump($tipocomida->getRutaImg());

            $temp = array(
                'id' => $tipocomida->getId(),
                'name' => $tipocomida->getName(),
                'icon' => $tipocomida->getIcon() ?? $tipocomida->getRutaImg()

            );
            array_push($newTipos, $temp);
        }

        die;

        $tamanoTrozo = ceil(count($newTipos) / 3);

        $tipos_separate = array_chunk($newTipos, $tamanoTrozo);

        $comidas_1 = $tipos_separate[0]; //$em->getRepository(TipoComida::class)->getPorIds(1, 4);
        $comidas_2 = $tipos_separate[1]; //$em->getRepository(TipoComida::class)->getPorIds(5, 8);
        $comidas_3 = $tipos_separate[2]; //$em->getRepository(TipoComida::class)->getPorIds(9, 11);

        $repoZonas = $this->entityManager->getRepository(Zonas::class);


        $zonaComedor = $repoZonas->findOneBy(['id' => 1]);
        $zonaTerraza = $repoZonas->findOneBy(['id' => 2]);
        $zonaBarra = $repoZonas->findOneBy(['id' => 3]);


        // $mesas_comedor = $em->getRepository(Mesas::class)->findBy([ "localizacion" => "C" ]);
        // $mesas_terraza = $em->getRepository(Mesas::class)->findBy([ "localizacion" => "T" ]);
        $mesas_comedor = $em->getRepository(Mesas::class)->mesasDashboardComedor();
        $mesas_terraza = $em->getRepository(Mesas::class)->mesasDashboardTerraza();
        $barra = $em->getRepository(Mesas::class)->findOneBy(["localizacion" => "Barra"]);

        $repoPers = $this->entityManager->getRepository(Personalizacion::class);
        $personalizacion = $repoPers->findOneBy(['active' => true]);
        // Verificar si $personalizacion no es null antes de llamar a getPath()
        if ($personalizacion !== null) {
            $request->getSession()->set('tabulator_route', $personalizacion->getPath());
        }
        $user = $this->getUser();

        $repoComida = $this->entityManager->getRepository(Comida::class);
        $isComida = $repoComida->findBy(['iscomida' => 1]);

        $list_iscomida = array();

        foreach ($isComida as $comida) {
            $temp = array(
                'id' => $comida->getId(),

            );
            array_push($list_iscomida, $temp);
        }

        $user = $this->getUser();

        if ($user) {
            // Verifica el rol del usuario actual
            $user = $this->security->getUser();
            if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_CAMARERO')) {
                return $this->render('dashboard/index.html.twig', [
                    'controller_name' => 'Bienvenido a MyBar',
                    'user' => $user,
                    'comida_1' => $comidas_1,
                    'comida_2' => $comidas_2,
                    'comida_3' => $comidas_3,
                    'comida_moviles' => $newTiposMovil,
                    'mesas' => $mesas_comedor,
                    'mesas_terraza' => $mesas_terraza,
                    'barra' => $barra->getNumero(),
                    'is_comida' => $list_iscomida,
                    'comedor' => $zonaComedor->isActive(),
                    'terraza' => $zonaTerraza->isActive(),
                    'barra_zona' => $zonaBarra->isActive(),
                ]);
            } elseif ($user->hasRole('ROLE_GESTOR')) {

                return $this->redirectToRoute('facturas');
            }
        } else {
            return $this->redirectToRoute('fos_user_security_login');
        }
    }

    /**
     * @Route("/addcomidamesa", name="add_comida_mesa")
     */
    public function addComidaMesa(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        // $food_stock = $em->getRepository(Stock::class)->findOneBy(['comida' => $request->request->get('food_id')]);

        // if ($food_stock) {
        //     if ($food_stock->getAmount() !== 0) {

        $pedido = new Pedidos();

        $pedidoDatos = $request->request->get('pedido');
        $food = null;

        $ordenPlato = "";
        if ($request->request->get('ordenplato') !== null) {
            $ordenPlato = $request->request->get('ordenplato');
        }

        $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $request->request->get('mesa_id')]);
        if ($pedidoDatos === null) {
            $food = $em->getRepository(Comida::class)->findOneBy(['id' => $request->request->get('food_id')]);
        } else {
            $repoPedidos = $this->entityManager->getRepository(Pedidos::class);
            $idPedido = $repoPedidos->findOneBy(['id' => $pedidoDatos]);
            $food = $em->getRepository(Comida::class)->findOneBy(['id' => $idPedido->getComida()->getId()]);
            $numPlato = $idPedido->getNumPlato();
            $ordenPlato = $numPlato;
        }

        $comentario = $request->request->get('comentario');
        $extras = $request->request->get('extras');
        $pagoExtras = 0;
        $arrayIdExtras = array();

        if ($extras) {
            foreach ($extras as $extra) {
                $repoComida = $this->entityManager->getRepository(Comida::class);
                $extraFood = $repoComida->findOneBy(['name' => $extra]);
                $pagoExtras += $extraFood->getPrecio();
                array_push($arrayIdExtras, $extraFood->getId());
            }
        }

        $precio_plato = floatval($food->getPrecio());
        $por_pagar_mesa = floatval($mesa->getPorpagar());

        $total_porpagar = $precio_plato + $por_pagar_mesa + $pagoExtras;
        $total_porpagar_string = sprintf("%.2f", $total_porpagar);

        $mesa->setPorPagar($total_porpagar_string);

        $pedido->setMesa($mesa);
        $pedido->setComida($food);
        $pedido->setComentarios($comentario);
        $pedido->setMarchando(0);
        $pedido->setExtras($arrayIdExtras);

        if ($ordenPlato !== "") {
            switch ($ordenPlato) {
                case '1':
                    $pedido->setNumPlato(1);
                    break;
                case '2':
                    $pedido->setNumPlato(2);
                    break;
                case '3':
                    $pedido->setNumPlato(3);
                    break;
                case '4':
                    $pedido->setNumPlato(4);
                    break;
                default:
                    // Acción por defecto si el ID no coincide con ninguno de los casos anteriores
                    echo "ID no reconocido";
                    break;
            }
        }

        $rand = rand(1, 9999);
        $dateRef = date('ymdhms');
        $mesaNum = $mesa->getNumero();
        $numRef = $mesaNum . '-' . $dateRef . $rand;

        $pedido->setNumRef($numRef);

        $em->persist($pedido);
        $em->persist($mesa);
        $em->flush();

        $datos_mesa = $mesa->getPedidos();

        $data_mesa = array();

        $precioExtra = 0;
        $extrasString = $food->getName();
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
        $temp = array(
            'comida' => $extrasString,
            'id' => $food->getId(),
            'precio' => $food->getPrecio() + $precioExtra,
            'pedido_mesa' => $pedido->getId()

        );
        array_push($data_mesa, $temp);

        $totalPagarZona = $this->entityManager->getRepository(Mesas::class)->totalZonas(intval($request->request->get('idzona')));


        return new JsonResponse([
            'pedido_mesa' => $data_mesa,
            'pedido' => true,
            'total_zona' => $totalPagarZona,
            'numplato' => $pedido->getNumPlato(),
        ]);
        //     } else {
        //         return new JsonResponse([
        //             'info' => 'No queda stock del producto',

        //         ]);
        //     }
        // }
    }

    /**
     * @Route("/listcomida", name="list_comida_mesa")
     */
    public function listComidaMesa(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $barra = $em->getRepository(Mesas::class)->findOneBy(['localizacion' => 'Barra']);
        $mesa = '';
        $num = intval($request->request->get('mesa_id'));
        if ($num !== $barra->getNumero()) {
            $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $num]);
        } else {
            $mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $num]);
        }


        $datos_mesa = $mesa->getPedidos();

        $data_mesa = array();
        foreach ($datos_mesa as $p_mesa) {
            $precioExtra = 0;
            $extrasString = $p_mesa->getComida()->getName();
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
                'id' => $p_mesa->getComida()->getId(),
                'precio' => $p_mesa->getComida()->getPrecio() + $precioExtra,

            );
            array_push($data_mesa, $temp);
        }


        return new JsonResponse([
            'pedido_mesa' => $data_mesa,

        ]);
    }

    /**
     * @Route("/addcomensales", name="add_comensales")
     */
    public function addComensales(Request $request)
    {

        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesa = $repoMesas->findOneBy(['numero' => $request->request->get('mesa')]);
        $mesa->setComensales(intval($request->request->get('numComensales')));
        $icon = '';

        if ($request->request->get('numComensales') == '0') {

            $icon = $mesa->getIcon();
        } else {

            $statscomensales = new Statscomensales();

            $statscomensales->setFecha(new \DateTime);
            $statscomensales->setMesa($mesa);
            // Obtener el número de comensales del request
            $numComensales = intval($request->request->get('numComensales'));

            // Asegurarte de que el número de comensales sea positivo
            $numComensales = abs($numComensales);

            $statscomensales->setNumComensales($numComensales);

            $this->entityManager->persist($statscomensales);
        }



        $this->entityManager->flush();

        $arrayComensales = $this->entityManager->getRepository(Mesas::class)->mesasConComensales();



        return new JsonResponse([

            'arraycomensales' => $arrayComensales,
            'icon' => $icon,


        ]);
    }
}
