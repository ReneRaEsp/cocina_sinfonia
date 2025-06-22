<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Facturas;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use DateTime;


class FacturasController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/facturas", name="facturas")
     */
    public function index(Request $request): Response
    {
        $repoFacturas = $this->entityManager->getRepository(Facturas::class);
        $facturas = $repoFacturas->findAll();

        // Obtener la fecha actual
        $fecha = new DateTime();

        // Definir los tramos
        $tramos = [
            ['inicio' => '01-01', 'fin' => '03-31', 'tramo' => 'Primer Tramo (Enero - Marzo)'],
            ['inicio' => '04-01', 'fin' => '06-30', 'tramo' => 'Segundo Tramo (Abril - Junio)'],
            ['inicio' => '07-01', 'fin' => '09-30', 'tramo' => 'Tercer Tramo (Julio - Septiembre)'],
            ['inicio' => '10-01', 'fin' => '12-31', 'tramo' => 'Cuarto Tramo (Octubre - Diciembre)']
        ];

        $data_facturas = array();
        $array_years = array();

        foreach ($facturas as $factura) {

            $temp = array(
                'id' => $factura->getId(),
                'nombre' => $factura->getNombre(),
                'fecha' => $factura->getFechaFactura()->format('d/m/Y'),
                'concepto' => $factura->getConcepto(),
                'importe' => $factura->getImporte(),
                'empresa' => $factura->getEmpresa(),
                'ruta' => $factura->getRutaPdf(),
                'tipo' => $factura->getTipo(),

            );

            array_push($data_facturas, $temp);

            $año = $factura->getFechaFactura()->format('Y');

            if (!in_array($año, $array_years)) {
                $array_years[] = $año;
            }
        };


        $json_facturas = str_replace('\\', '\\\\', $data_facturas);


        $new_factura = new Facturas();
        //Creamos en formulario para añadir un factura
        $form = $this->createFormBuilder($new_factura)
            ->add('nombre', TextType::class, array('label' => 'Nombre'))
            ->add('concepto', TextType::class, array('label' => 'Concepto'))
            ->add('fecha_factura', DateTimeType::class, array(
                'label' => 'Fecha y hora',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'html5' => false,
                'attr' => array('class' => 'flatpickr'),

            ))
            ->add('empresa', TextType::class, array('label' => 'Empresa'))
            ->add('importe', TextType::class, array('label' => 'Importe'))
            ->add('ruta_pdf', FileType::class, array('label' => 'Archivo PDF'))
            ->add('tipo', ChoiceType::class, [
                'choices' => [
                    'Emitidas' => 'Emitidas',
                    'Recibidas' => 'Recibidas',
                ],
                'placeholder' => 'Selecciona un tipo',
            ])
            ->add('submit', SubmitType::class, array('label' => 'Crear factura'))
            ->getForm();

        //Comprobamos si el formulario  ha sido enviado  y en ese caso guardamos el objeto en la bbdd
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_factura_1 = new Facturas();
            // guardar el objeto en la base de datos
            $new_factura_1->setNombre($form['nombre']->getData());
            $new_factura_1->setConcepto($form['concepto']->getData());
            $new_factura_1->setFechaFactura($form['fecha_factura']->getData());
            $new_factura_1->setEmpresa($form['empresa']->getData());

            // Obtener el valor del formulario
            $importeFromForm = $form['importe']->getData();

            // Reemplazar los puntos por una cadena vacía y la coma por un punto para obtener un número válido
            $importeForDatabase = str_replace('.', '', $importeFromForm); // Elimina los puntos de separación de miles
            $importeForDatabase = str_replace(',', '.', $importeForDatabase); // Reemplaza la coma por un punto decimal

            // Convertir a formato adecuado para DECIMAL(10,2)
            $importeForDatabase = (float) $importeForDatabase;

            $new_factura_1->setImporte($importeForDatabase);

            // Obtener el archivo PDF subido
            $archivoPdf = $form['ruta_pdf']->getData();

            // Generar un nombre de archivo único para el PDF
            $nombreArchivo = uniqid() . '.' . $archivoPdf->getClientOriginalExtension();

            // Mover el archivo PDF a la carpeta "pdfs" en el directorio "public"
            $archivoPdf->move($this->getParameter('Pdf'), $nombreArchivo);

            $new_factura_1->setRutaPdf('/pdf/' . $nombreArchivo);

            $new_factura_1->setTipo($form['tipo']->getData());
            //Con estas dos llamadas guardamos y actualizamos la base de datos
            $this->entityManager->persist($new_factura_1);
            $this->entityManager->flush();

            // Crea un nuevo objeto vacío y asignarlo al formulario
            $form = $this->createFormBuilder(new Facturas())
                ->add('nombre', TextType::class, array('label' => 'Nombre'))
                ->add('concepto', TextType::class, array('label' => 'Concepto'))
                ->add('fecha_factura', DateTimeType::class, array(
                    'label' => 'Fecha y hora',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd HH:mm:ss',
                    'html5' => false,
                    'attr' => array('class' => 'flatpickr'),
                ))
                ->add('empresa', TextType::class, array('label' => 'Empresa'))
                ->add('importe', TextType::class, array('label' => 'Importe'))
                ->add('ruta_pdf', FileType::class, array('label' => 'Archivo PDF'))
                ->add('tipo', ChoiceType::class, [
                    'choices' => [
                        'Emitidas' => 'Emitidas',
                        'Recibidas' => 'Recibidas',
                    ],
                    'placeholder' => 'Selecciona un tipo',
                ])
                ->add('submit', SubmitType::class, array('label' => 'Crear factura'))
                ->getForm();

            $repoFacturas = $this->entityManager->getRepository(Facturas::class);
            $facturas_2 = $repoFacturas->findAll();

            $data_2 = array();
            foreach ($facturas_2 as $factura) {
                $temp = array(
                    'id' => $factura->getId(),
                    'nombre' => $factura->getNombre(),
                    'fecha' => $factura->getFechaFactura()->format('d-m-Y'),
                    'concepto' => $factura->getConcepto(),
                    'importe' => $factura->getImporte(),
                    'empresa' => $factura->getEmpresa(),
                    'ruta' => $factura->getRutaPdf(),
                    'tipo' => $factura->getTipo(),

                );

                array_push($data_2, $temp);
            };

            $json_facturas = str_replace('\\', '\\\\', $data_2);

            $inicioTramo = 0;
            $finTramo = 0;
            $tramo = '';

            // Iterar a través de los tramos y verificar si la fecha actual está en uno de ellos
            foreach ($tramos as $tramo) {
                $inicio = new DateTime($fecha->format('Y') . '-' . $tramo['inicio']);
                $fin = new DateTime($fecha->format('Y') . '-' . $tramo['fin']);

                // Verificar si la fecha actual está en el tramo
                if ($fecha >= $inicio && $fecha <= $fin) {

                    $inicioTramo = $inicio;
                    $finTramo = $fin;
                    break;
                }
            }

            $totalImpuesto = 0;
            $iva0 = 0;
            $iva10 = 0;
            $iva5 = 0;
            $iva21 = 0;
            foreach ($facturas as $factura) {
                if ($factura->getFechaFactura() >= $inicioTramo && $factura->getFechaFactura() <= $finTramo) {
                    foreach ($factura->getImpuestoFacturas() as $impuesto) {
                        if ($impuesto->getImpuesto() === 0) {

                            $iva0 += $impuesto->getCantidad();
                            $totalImpuesto += $impuesto->getCantidad();
                        } else if ($impuesto->getImpuesto() === 5) {

                            $iva5 += $impuesto->getCantidad();
                            $totalImpuesto += $impuesto->getCantidad();
                        } else if ($impuesto->getImpuesto() === 10) {

                            $iva10 += $impuesto->getCantidad();
                            $totalImpuesto += $impuesto->getCantidad();
                        } else if ($impuesto->getImpuesto() === 21) {

                            $iva21 += $impuesto->getCantidad();
                            $totalImpuesto += $impuesto->getCantidad();
                        }
                    }
                }
            }


            return $this->render('facturas/index.html.twig', [
                'facturas' => $data_2,
                'form' => $form->createView(),
                'bill_added' => true,
                'tramo' => $tramo,
                'fechaInicio' => $inicioTramo,
                'fechaFin' => $finTramo,
                'años' => $array_years,
                'iva0' => $iva0,
                'iva5' => $iva5,
                'iva10' => $iva10,
                'iva21' => $iva21,
                'totalimpuestos' => $totalImpuesto

            ]);
        }



        $inicioTramo = 0;
        $finTramo = 0;
        $inicioTramoString = '';
        $finTramoString = '';
        $tramo = '';

        // Iterar a través de los tramos y verificar si la fecha actual está en uno de ellos
        foreach ($tramos as $tramo) {
            $inicio = new DateTime($fecha->format('Y') . '-' . $tramo['inicio']);
            $fin = new DateTime($fecha->format('Y') . '-' . $tramo['fin']);

            // Verificar si la fecha actual está en el tramo
            if ($fecha >= $inicio && $fecha <= $fin) {

                $inicioTramo = $inicio;
                $finTramo = $fin;
                $inicioTramoString = $tramo['inicio'];
                $finTramoString = $tramo['fin'];
                $tramo = $tramo['tramo'];
                break;
            }
        }

        $totalImpuesto = 0;
        $iva0 = 0;
        $iva10 = 0;
        $iva5 = 0;
        $iva21 = 0;
        foreach ($facturas as $factura) {
            if ($factura->getFechaFactura() >= $inicioTramo && $factura->getFechaFactura() <= $finTramo) {
                foreach ($factura->getImpuestoFacturas() as $impuesto) {
                    if ($impuesto->getImpuesto() === 0) {

                        $iva0 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 5) {

                        $iva5 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 10) {

                        $iva10 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 21) {

                        $iva21 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    }
                }
            }
        }




        return $this->render('facturas/index.html.twig', [
            'facturas' => $data_facturas,
            'form' => $form->createView(),
            'bill_added' => false,
            'tramo' => $tramo,
            'fechaInicio' => $inicioTramoString,
            'fechaFin' => $finTramoString,
            'años' => $array_years,
            'iva0' => $iva0,
            'iva5' => $iva5,
            'iva10' => $iva10,
            'iva21' => $iva21,
            'totalimpuestos' => $totalImpuesto
        ]);
    }


    /**
     * @Route("/setfacturas", name="setfacturas")
     */
    public function setFactura(Request $request)
    {

        $id = $request->request->get('id');
        $value = $request->request->get('value');
        $field = $request->request->get('colum'); // asegurarse que esta variable esta definida

        $repoFacturas = $this->entityManager->getRepository(Facturas::class);
        $factura = $repoFacturas->findOneBy(['id' => $id]);

        //$stock->setName($value);
        switch ($field) {
            case 'nombre':
                $factura->setNombre($value);
                break;
            case 'concepto':
                $factura->setConcepto($value);
                break;
            case 'fecha':
                $factura->setFechaFactura(new DateTime($value));
                break;
            case 'empresa':
                $factura->setEmpresa($value);
                break;
            case 'importe':
                $factura->setImporte($value);
                break;
            case 'tipo':
                $factura->setTipo($value);
                break;
            default:
                return new JsonResponse([
                    'error' => 'Campo no válido',
                ]);
        }

        $this->entityManager->persist($factura);
        $this->entityManager->flush();


        return new JsonResponse([
            'factura' => 'La factura se ha actualizado correctamente',

        ]);
    }
    /**
     * @Route("/deletefacturas", name="deletefacturas")
     */
    public function deleteFacturas(Request $request): Response
    {

        $id = $request->query->get('id');
        $rootPath = $this->getParameter('kernel.project_dir');
        $ruta = $rootPath . '/public' . strtolower($request->request->get('ruta'));

        $repoFacturas = $this->entityManager->getRepository(Facturas::class);
        $factura = $repoFacturas->findOneBy(['id' => $id]);

        $this->entityManager->remove($factura);
        $this->entityManager->flush();

        $facturas_2 = $repoFacturas->findAll();
        $data_2 = array();
        foreach ($facturas_2 as $factura) {
            $temp = array(
                'id' => $factura->getId(),
                'nombre' => $factura->getNombre(),
                'fecha' => $factura->getFechaFactura()->format('d-m-Y'),
                'concepto' => $factura->getConcepto(),
                'importe' => $factura->getImporte(),
                'empresa' => $factura->getEmpresa(),
                'ruta' => str_replace('\\', '\\\\', $factura->getRutaPdf()), // Escapar las barras invertidas en la ruta
                'tipo' => $factura->getTipo(),

            );

            array_push($data_2, $temp);
        };

        $json_facturas = str_replace('\\', '\\\\', $data_2);



        if (file_exists($ruta)) {
            $contenido = file_get_contents($ruta . '/' . $factura->getRutaPdf());
            if ($contenido !== false) {
                if (unlink($ruta . '/' . $factura->getRutaPdf())) {
                    return new JsonResponse([
                        'factura' => 'La factura se ha eliminado correctamente',
                        'facturas' => $json_facturas
                    ], 200);
                } else {
                    return new JsonResponse([
                        'factura' => 'La factura no se ha eliminado por un error al intentar eliminar el archivo',
                    ], 404);
                }
            } else {
                return new JsonResponse([
                    'factura' => 'La factura no se ha eliminado por un error al intentar leer el archivo',
                ], 404);
            }
        } else {
            return new JsonResponse([
                'factura' => 'La factura no se ha eliminado porque el archivo no existe',
            ], 404);
        }
    }
    /**
     * @Route("/buscartramo", name="buscar_tramo")
     */
    public function buscarTramo(Request $request): Response
    {

        $numTramo = intval($request->request->get('tramo'));
        $año = intval($request->request->get('año'));


        $repoFacturas = $this->entityManager->getRepository(Facturas::class);
        $facturas = $repoFacturas->findAll();


        // Definir los tramos
        $tramos = [
            ['inicio' => '01-01', 'fin' => '03-31', 'tramo' => 'Primer Tramo (Enero - Marzo)'],
            ['inicio' => '04-01', 'fin' => '06-30', 'tramo' => 'Segundo Tramo (Abril - Junio)'],
            ['inicio' => '07-01', 'fin' => '09-30', 'tramo' => 'Tercer Tramo (Julio - Septiembre)'],
            ['inicio' => '10-01', 'fin' => '12-31', 'tramo' => 'Cuarto Tramo (Octubre - Diciembre)']
        ];

        $inicioTramo = 0;
        $finTramo = 0;



        $inicio = new DateTime($año . '-' . $tramos[$numTramo]['inicio']);
        $fin = new DateTime($año . '-' . $tramos[$numTramo]['fin']);
        $tramo = $tramos[$numTramo]['tramo'];


        $inicioTramo = $inicio;
        $finTramo = $fin;


        $totalImpuesto = 0;
        $iva0 = 0;
        $iva10 = 0;
        $iva5 = 0;
        $iva21 = 0;
        foreach ($facturas as $factura) {
            if ($factura->getFechaFactura() >= $inicioTramo && $factura->getFechaFactura() <= $finTramo) {
                foreach ($factura->getImpuestoFacturas() as $impuesto) {
                    if ($impuesto->getImpuesto() === 0) {

                        $iva0 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 5) {

                        $iva5 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 10) {

                        $iva10 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 21) {

                        $iva21 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    }
                }
            }
        }


        return new JsonResponse([
            'tramo' => $tramo,
            'iva0' => $iva0,
            'iva5' => $iva5,
            'iva10' => $iva10,
            'iva21' => $iva21,
            'totalimpuestos' => $totalImpuesto
        ]);
    }

    /**
     * @Route("/fechatramo", name="fecha_tramo")
     */
    public function fechaTramo(Request $request): Response
    {

        $fecha = $request->request->get('fecha');


        $repoFacturas = $this->entityManager->getRepository(Facturas::class);
        $facturas = $repoFacturas->findAll();


        // Definir los tramos
        $tramos = [
            ['inicio' => '01-01', 'fin' => '03-31', 'tramo' => 'Primer Tramo (Enero - Marzo)'],
            ['inicio' => '04-01', 'fin' => '06-30', 'tramo' => 'Segundo Tramo (Abril - Junio)'],
            ['inicio' => '07-01', 'fin' => '09-30', 'tramo' => 'Tercer Tramo (Julio - Septiembre)'],
            ['inicio' => '10-01', 'fin' => '12-31', 'tramo' => 'Cuarto Tramo (Octubre - Diciembre)']
        ];



        $fecha = new DateTime($fecha);

        // var_dump($fecha->format('d-m-Y'));


        $totalImpuesto = 0;
        $iva0 = 0;
        $iva10 = 0;
        $iva5 = 0;
        $iva21 = 0;
        foreach ($facturas as $factura) {
            if ($factura->getFechaFactura()->format('d-m-Y') === $fecha->format('d-m-Y')) {
                foreach ($factura->getImpuestoFacturas() as $impuesto) {
                    if ($impuesto->getImpuesto() === 0) {

                        $iva0 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 5) {

                        $iva5 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 10) {

                        $iva10 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    } else if ($impuesto->getImpuesto() === 21) {

                        $iva21 += $impuesto->getCantidad();
                        $totalImpuesto += $impuesto->getCantidad();
                    }
                }
            }
        }





        return new JsonResponse([
            'fecha' => $fecha->format('d-m-Y'),
            'iva0' => $iva0,
            'iva5' => $iva5,
            'iva10' => $iva10,
            'iva21' => $iva21,
            'totalimpuestos' => $totalImpuesto
        ]);
    }
}
