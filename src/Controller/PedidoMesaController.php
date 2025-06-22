<?php

namespace App\Controller;

use App\Entity\Comida;
use App\Entity\Mesas;
use App\Entity\Pedidos;
use App\Entity\TipoComida;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Length;

class PedidoMesaController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/pedido/mesa/{numMesa}", name="app_pedido_mesa")
     */
    public function index($numMesa): Response
    {

        $repoTipoComida = $this->entityManager->getRepository(TipoComida::class);
        $repoComida = $this->entityManager->getRepository(Comida::class);
        $comidaExtras = $repoComida->findBy(['extra' => true]);
        $tiposComida = $repoTipoComida->findAll();

        $arrayPedidos = array();
        $arrayComida = array();
        foreach ($tiposComida as $tipo) {

            $arrayPedidos[] = array(
                "name" => $tipo->getName()

            );

            $comidasAsociadas = array();

            // Suponiendo que hay una relación entre TipoComida y Comida
            $comidas = $tipo->getComida();

            foreach ($comidas as $comida) {
                $arraySup = array();
                foreach ($comida->getPosiblesextras() as $sup) {

                    $extraComida = $repoComida->findOneBy(['id' => $sup]);
                    array_push($arraySup, $extraComida->getName());
                }
                $comidasAsociadas[] = array(
                    "id" => $comida->getId(),
                    "name" => $comida->getName(),
                    "desc" => $comida->getDescription(),
                    "precio" => number_format($comida->getPrecio(), 2,),
                    "extras" => $arraySup
                    // Puedes agregar más atributos de las comidas aquí si es necesario
                );
            }

            $arrayComida[] = array(
                "" . $tipo->getName() . "" => $comidasAsociadas
            );
        }

        $arrayExtras = array();
        foreach ($comidaExtras as $extra) {
            $arrayExtras[] = array(
                "nombre" => $extra->getName(),
                "precio" => $extra->getPrecio(),
            );
        }





        return $this->render('pedido_mesa/index.html.twig', [
            'controller_name' => 'PedidoMesaController',
            'tiposDeComida' => $arrayPedidos,
            'comidaTipo' => $arrayComida,
            'numMesa' => $numMesa,
            "extras" => $arrayExtras,

        ]);
    }
    /**
     * @Route("addpedido/mesa", name="add_pedido")
     */
    public function addPedidoMesa(Request $request): Response
    {

        $pedidos = $request->request->get('pedido');
        $nummesa = $request->request->get('nummesa');




        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $uniones = $repoMesas->mesasUnidas();


        if ($uniones) {
            foreach ($uniones as $union) {
                $arrayUnidas = explode(',', $union["union_mesas"]);

                for ($i = 0; $i < count($arrayUnidas); $i++) {

                    if ($arrayUnidas[$i] === $nummesa) {
                        $nummesa = $union["numero"];
                        break;
                    }
                }
            }
        }


        $mesa = $repoMesas->findOneBy(['numero' => $nummesa]);



        $porpagar = 0;

        foreach ($pedidos as $pedido) {
            $newPedido = new Pedidos();
            $repoComida = $this->entityManager->getRepository(Comida::class);
            $comida = $repoComida->findOneBy(['id' => $pedido['id']]);
            $porpagar += $comida->getPrecio();
            $arrayExtras = $pedido['extra'] ? explode(',', $pedido['extra']) : false;

            if ($arrayExtras) {
                $arrayExtrasIds = array();

                foreach ($arrayExtras as $extra) {
                    $precioExtra = $repoComida->findOneBy(['name' => $extra]);
                    $porpagar += floatval($precioExtra->getPrecio());
                    array_push($arrayExtrasIds, $precioExtra->getId());
                }

                $newPedido->setExtras($arrayExtrasIds);
            }

            $newPedido->setMesa($mesa);
            $newPedido->setComida($comida);
            $newPedido->setMarchando(true);

            $this->entityManager->persist($newPedido);
        }

        $mesa->setPorPagar($mesa->getPorPagar() + $porpagar);
        $this->entityManager->persist($mesa);

        $this->entityManager->flush();


        return new JsonResponse([]);
    }

    /**
     * @Route("/setpedidoentregado", name="set_pedido_entregado", methods={"POST"})
     */
    public function setPedidoEntregado(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $pedidoStock = $em->getRepository(Pedidos::class);

        //Obtener los datos del JSON 
        $ids = $data['ids'];

        foreach ($ids as $id) {
            $pedido = $pedidoStock->findOneBy(['id' => $id]);

            if (!$pedido) {
                return $this->json(['error' => 'Pedido no encontrado'], 404);
            }
            $pedido->setEntregado(true);
            $em->persist($pedido);
        }

        $em->flush();

        return $this->json(['message' => 'Pedido actualizado a entregado']);
    }


    /**
     * @Route("/testsetpedidoentregado", name="test_set_pedido_entregado", methods={"POST"})
     */
    public function testsetPedidoEntregado(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $em = $this->getDoctrine()->getManager();
        $pedidoStock = $em->getRepository(Pedidos::class);

        //Obtener los datos del JSON 
        $id = $data['id'];

        $pedido = $pedidoStock->findOneBy(['id' => $id]);

        if (!$pedido) {
            return $this->json(['error' => 'Pedido no encontrado'], 404);
        }


        $pedido->setEntregado(true);

        $em->persist($pedido);
        $em->flush();

        return $this->json(['message' => 'Pedido actualizado a entregado']);
    }
}
