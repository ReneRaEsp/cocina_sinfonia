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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CocinaController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/cocina", name="app_cocina")
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();
        $mesas = $em->getRepository(Mesas::class)->findAll();
        $mesasData = [];

        foreach ($mesas as $item) {
            $temp = array(
                'id' => $item->getId(),
                'numero' => $item->getNumero(),
                'localizacion' => $item->getLocalizacion()
            );
            array_push($mesasData, $temp);
        }

        return $this->render('cocina/index.html.twig', [
            'mesas' => $mesasData,
        ]);
    }

    /**
     * @Route("/listallpedidos", name="list_all_pedidos")
     */
    public function listAllPedidos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Pedidos::class);
        $query = $repository->createQueryBuilder('p')->leftJoin('p.comida', 'c')->getQuery();

        $data = $query->getResult();

        $pedidos = [];

        foreach ($data as $item) {
            if ($item->getComida()) {
                $temp = array(
                    'id' => $item->getId(),
                    'comida' => $item->getComida()->getName(),
                    'tipo_comida' => $item->getComida()->getTypeFood()->getName(),
                    'id_mesa' => $item->getMesa()->getId(),
                    'mesa' => $item->getMesa()->getNumero(),
                    'is_bebida' => $item->getComida()->isIsbebida(),
                    'is_comida'  => $item->getComida()->isIscomida(),
                    'marchando' => $item->isMarchando(),
                    'estado' => $item->getEstado(),
                    'num_plato' => $item->getNumPlato(),
                    'entregado' => $item->isEntregado(),
                    'estado_plato' => $item->getEstadoPlato()
                );
                array_push($pedidos, $temp);
            }
        }

        return new JsonResponse([
            'pedidos' => $pedidos,
        ]);
    }

    /**
     * @Route("/listallmesas", name="list_all_mesas")
     */
    public function listAllMesas(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $mesas = $em->getRepository(Mesas::class)->findAll();

        foreach ($mesas as $item) {
            $temp = array(
                'id' => $item->getId(),
                'numero' => $item->getNumero(),
                'localizacion' => $item->getLocalizacion()
            );
            array_push($mesas, $temp);
        }

        return new JsonResponse([
            'mesas' => $mesas,
        ]);
    }

    /**
     * @Route("/listpedidosbymesa", name="list_pedidos_mesa")
     */
    public function listPedidosByMesa(Request $request)
    {
        $id = $request->query->get('id');
        $id = intval($id);

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Pedidos::class);
        $query = $repository->findBy(['mesa' => $id]);

        $data = $query;

        $pedidos = [];

        foreach ($data as $item) {
            $temp = array(
                'id' => $item->getId(),
                'comida' => $item->getComida()->getName(),
                'tipo_comida' => $item->getComida()->getTypeFood()->getName(),
                'id_mesa' => $item->getMesa()->getId(),
                'mesa' => $item->getMesa()->getNumero(),
                'marchando' => $item->isMarchando(),
                'entregado' => $item->isEntregado()
            );
            array_push($pedidos, $temp);
        }

        return new JsonResponse([
            'pedidos' => $pedidos,
        ]);
    }

    /**
     * @Route("/setmarchando", name="set_marchando", methods={"PUT"})
     */
    public function setMarchando(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Pedidos::class);

        $id = intval($id);
        $pedido = $repository->find($id);
        $pedido->setPedido(1);

        $pedidoData = array(
            'id' => $pedido->getId(),
            'comida' => $pedido->getComida()->getName(),
            'tipo_comida' => $pedido->getComida()->getTypeFood()->getName(),
            'id_mesa' => $pedido->getMesa()->getId(),
            'mesa' => $pedido->getMesa()->getNumero(),
            'marchando' => $pedido->isMarchando(),
            'estado' => $pedido->getEstado(),
            'entregado' => $pedido->isEntregado()
        );

        $em->flush();
        return new JsonResponse(['pedido' => $pedidoData]);
    }

    /**
     * @Route("/setestado", name="set_estado", methods={"PUT"})
     */
    public function setEstado(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $newEstado = $data['newEstado'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Pedidos::class);

        $id = intval($id);
        $newEstado = intval($newEstado);
        $pedido = $repository->find($id);
        $estado = $pedido->getEstado();

        $pedido->setEstado($newEstado);

        $pedidoData = array(
            'id' => $pedido->getId(),
            'comida' => $pedido->getComida()->getName(),
            'tipo_comida' => $pedido->getComida()->getTypeFood()->getName(),
            'id_mesa' => $pedido->getMesa()->getId(),
            'mesa' => $pedido->getMesa()->getNumero(),
            'marchando' => $pedido->isMarchando(),
            'estado' => $pedido->getEstado(),
            'entregado' => $pedido->isEntregado()
        );

        $em->flush();
        return new JsonResponse(['pedido' => $pedidoData]);
    }

    /**
     * @Route("/setPedidoStatus", name="set_pedido_status", methods={"PUT"})
     */
    public function setPedidoStatus(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];
        $newEstado = $data['newEstado'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Pedidos::class);

        $id = intval($id);
        $newEstado = intval($newEstado);
        $pedido = $repository->find($id);

        $pedido->setEstadoPlato($newEstado);

        $em->flush();
        return new JsonResponse(['msg' => 'Pedido actualizado exitosamente', 'newEstado' => $pedido]);
    }
}
