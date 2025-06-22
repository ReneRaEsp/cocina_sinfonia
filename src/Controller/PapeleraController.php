<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Pedidos;
use App\Entity\Mesas;
use App\Entity\Papelera;
use App\Entity\Zonas;
use App\Entity\Comida;

class PapeleraController extends AbstractController
{
    /**
     * @Route("/papelera", name="app_papelera")
     */
    public function index(): Response
    {
        return $this->render('papelera/index.html.twig', [
            'controller_name' => 'PapeleraController',
        ]);
    }


    /**
     * @Route("/listpapelera", name="list_papelera")
     */
    public function listAllPapelera(): Response
    {
  	$em = $this->getDoctrine()->getManager();
	$papelera = $em->getRepository(Papelera::class)->findAll();

	$papeleraData = [];
	foreach ($papelera as $item) {
		$pedidos = $item->getPedidos();
		$pedidos = json_decode($pedidos);
		$temp = array('id' => $item->getId(), 'numero' => $item->getNumero(), 'pagado' => $item->getPagado(), 'por_pagar'=> $item->getPorPagar(), 'union_mesas' => $item->getUnionMesas(), 'localizacion' => $item->getLocalizacion(), 'zonas_id' => $item->getZonasId(), 'comensales' => $item->getComensales(), 'factura' => $item->getFactura(), 'pedidos' => $pedidos);
	array_push($papeleraData, $temp);
	}

	return new JsonResponse(['papelera' => $papeleraData]);
    }


    /**
     *  @Route("/copiarmesa", name="copiar_mesa", methods={"POST"})
     */
    public function copiarMesa(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

	$data = json_decode($request->getContent(), true);
	$pedidos = $data['pedidos'];
	$numeroMesa = $data['numeroMesa'];

	$mesa = $em->getRepository(Mesas::class)->findOneBy(['numero' => $numeroMesa]);

	$mesaData = array('id' => $mesa->getId(), 'numero' => $mesa->getNumero(), 'pagado' => $mesa->getPagado(), 'por_pagar'=> $mesa->getPorPagar(), 'union_mesas' => $mesa->getUnionMesas(), 'localizacion' => $mesa->getLocalizacion(), 'zonas_id' => $mesa->getZonas()->getId(), 'comensales' => $mesa->getComensales(), 'factura' => $mesa->isFactura());

	$pedidosArr = [];
 
	foreach ($pedidos as $pedido) {
		$pedido = intval($pedido);
		$pedido = $em->getRepository(Pedidos::class)->findOneBy(['id' => $pedido]);
		$temp = array('id' => $pedido->getId(),  'pedido' => $pedido->getComida() ? $pedido->getComida()->getName() : $pedido->getProducttienda()->getNombre(), 'mesa_id' => $pedido->getMesa()->getId(), 'comida_id' => $pedido->getComida() ? $pedido->getComida()->getId() : $pedido->getProducttienda()->getId(), 'comentarios' => $pedido->getComentarios(), 'invitacion' => $pedido->getDescuento(),  'descuento' => $pedido->getDescuento(), 'descuento_eur' => $pedido->getDescuentoEur(), 'estado' => $pedido->getEstado(),  'marchando' => $pedido->isMarchando(), 'num_plato' => $pedido->getNumPlato(), 'num_ref' => $pedido->getNumRef());
		array_push($pedidosArr, $temp);
	}
	$pedidosArr = json_encode($pedidosArr);

	$newPapelera = New Papelera();
	$newPapelera->setNumero($mesaData['numero']);
	$newPapelera->setPagado($mesaData['pagado']);
	$newPapelera->setPorPagar($mesaData['por_pagar']);
	$newPapelera->setUnionMesas($mesaData['union_mesas']);
	$newPapelera->setLocalizacion($mesaData['localizacion']);
	$newPapelera->setZonasId($mesaData['zonas_id']);
	$newPapelera->setComensales($mesaData['comensales']);
	$newPapelera->setFactura($mesaData['factura']);
	$newPapelera->setPedidos($pedidosArr);

	$em->persist($newPapelera);
	$em->flush();

	return new JsonResponse(['mesa' => 'Exito']);
    }

    /**
     *  @Route("/restaurarmesa", name="restaurar_mesa", methods={"POST"})
     */
    public function restaurarMesa(Request $request)
    {
	$em = $this->getDoctrine()->getManager();

	$data = json_decode($request->getContent(), true);
	$id = $data['id'];
	$id = intval($id);

	$papelera = $em->getRepository(Papelera::class)->findOneBy(['id' => $id]);
	$repoComida = $em->getRepository(Comida::class);
	$repoMesas = $em->getRepository(Mesas::class);

	$pedidos = $papelera->getPedidos();
	$pedidos = json_decode($pedidos);

	$mesa = [];
	foreach ($pedidos as $pedido) {
		$newPedido = new Pedidos();
		
		$mesa = $repoMesas->findOneBy(['id' => $pedido->mesa_id]);
		$newPedido->setMesa($mesa);
		
		$comida = $repoComida->findOneBy(['id' => $pedido->comida_id]);
		$newPedido->setComida($comida);
		
		$newPedido->setMarchando($pedido->marchando);
		$newPedido->setInvitacion($pedido->invitacion);
		$newPedido->setDescuentoEur($pedido->descuento_eur);
		$newPedido->setEstado($pedido->estado);
		$newPedido->setNumPlato($pedido->num_plato);
		$newPedido->setNumRef($pedido->num_ref);

		$em->persist($newPedido);
		$em->flush();
	}

	$mesa = $repoMesas->findOneBy(['numero' => $papelera->getNumero()]);
	$mesa->setPorPagar($papelera->getPorPagar());
	$mesa->setComensales($papelera->getComensales());

	$em->persist($mesa);

	$em->remove($papelera);
	$em->flush();

	return new JsonResponse(['msg' => 'Exito']);
    }

    /**
     *  @Route("/vaciarpapelera", name="vaciar_papelera", methods={"DELETE"})
     */
    public function vaciarPapelera(Request $request)
    {
	$em = $this->getDoctrine()->getManager();
	$query = $em->createQuery('DELETE FROM App\Entity\Papelera');
	$query->execute();

	return new JsonResponse(['mesa' => 'Exito']);
    }
}
