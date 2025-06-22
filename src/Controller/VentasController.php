<?php

namespace App\Controller;

use App\Entity\Cajaregistro;
use App\Entity\Info;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Ventas;
use App\Entity\Mesas;
use App\Entity\Tickettofactura;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use function PHPUnit\Framework\isNull;

class VentasController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/ventas", name="ventas")
     */
    public function index(): Response
    {

        $em = $this->getDoctrine()->getManager();
        $ventas = $em->getRepository(Ventas::class)->createQueryBuilder('v')->orderBy('v.fecha', 'DESC')->getQuery()->getResult();

        $mesesEnEspanol = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $fechaActual = new \DateTime('now');
        $mesActual = $mesesEnEspanol[(int)$fechaActual->format('n')];

        // Obtener el nombre del mes anterior en español
        $fechaMesAnterior = new \DateTime('first day of ' . $fechaActual->format('Y-m') . ' -1 month');
        $mesAnterior = $mesesEnEspanol[(int)$fechaMesAnterior->format('n')];

        // Obtener el nombre de dos meses anteriores en español
        $fechaDosMesesAntes = new \DateTime('first day of ' . $fechaActual->format('Y-m') . ' -2 months');
        $dosMesesAntes = $mesesEnEspanol[(int)$fechaDosMesesAntes->format('n')];

        $totalVentasMesActual = $em->getRepository(Ventas::class)->obtenerVentasDelMesActual();
        $totalVentasMesAnterior = $em->getRepository(Ventas::class)->obtenerVentasDelMesAnterior();
        $totalVentasDosMesesAnteriores = $em->getRepository(Ventas::class)->obtenerVentasDeDosMesesAnteriores();

        $data = array();
        foreach ($ventas as $venta) {
            $pedidosRef = $venta->getPedidosRef();
            $pedidosRef = json_decode($pedidosRef);
            $name_factura = null;
            if (is_array($pedidosRef) && isset($pedidosRef[0])) {
                $name_factura = $this->entityManager->getRepository(Tickettofactura::class)->findOneBy(['ref' => $pedidosRef[0]]);
            }
            $result = '-';
            if (isset($name_factura)) {
                $ruta = $name_factura->getRuta();
                $parts = explode('/', $ruta);
                $parts2 = explode('.', $parts[2]);
                $result = $parts2[0].'✅';
            }
            $temp = array(
                'id' => $venta->getId(),
                'mesa' => $venta->getNumMesa(),
                'fecha' => $venta->getFecha()->format('d/m/Y'),
                'cantidad' => $venta->getPagado(),
                'metodo_pago' => $venta->getPago(),
                'observaciones' => $venta->getObservaciones(),
                'refticket' => isset($pedidosRef[0]) ? $pedidosRef[0] : '',
                'pedidos_ref' => $pedidosRef,
                'iva' => $venta->getIva(),
                'importeiva' => $venta->getImporteIva() . ' €',
                'name_factu' => $result,
            );
            array_push($data, $temp);
        }

        $metodo_pago = array('Tarjeta', 'Efectivo');

        function formatNumberToEuro($number)
        {
            return number_format($number, 2, ',', '.');
        }

        $this->entityManager->flush();

        return $this->render('ventas/index.html.twig', [
            'controller_name' => 'VentasController',
            'ventas' => $data,
            'mpagos' => $metodo_pago,
            'ventasMesActual' => [
                'total' => formatNumberToEuro($totalVentasMesActual),
                'mes' => $mesActual,
            ],
            'ventasMesAnterior' => [
                'total' => formatNumberToEuro($totalVentasMesAnterior),
                'mes' => $mesAnterior,
            ],
            'ventasDosMesesAnteriores' => [
                'total' => formatNumberToEuro($totalVentasDosMesesAnteriores),
                'mes' => $dosMesesAntes,
            ],
        ]);
    }

    /**
     * @Route("/datosempresa", name="datos_empresa")
     */
    public function datosEmpresa(Request $request)
    {

        $dato = $this->entityManager->getRepository(Info::class)->findOneBy(['id' => 1]);


        $array_datos = array(
            'nombre' => $dato->getName(),
            'direccion' => $dato->getDir(),
            'telefono' => $dato->getTelf(),
            'email' => $dato->getEmail(),
            'logo' => $dato->getLogo() ? $dato->getLogo() : '',
            'cif' => $dato->getCif(),

        );



        return new JsonResponse([
            'datos' => $array_datos,

        ], 200);
    }
}
