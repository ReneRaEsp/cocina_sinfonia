<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Mindee\Client;
use Mindee\Product\Invoice\InvoiceV4;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Facturas;
use App\Entity\ImpuestoFacturas;




class PDFController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/pdf", name="app_pdf")
     */
    public function index(): Response
    {
        return $this->render('pdf/index.html.twig', [
            'controller_name' => 'PDFController',
        ]);
    }

    /**
     * @Route("/uploadpdfmanual", name="upload_pdf_manual")
     */
    public function uploadPdfManual (Request $request, SluggerInterface $slugger): Response
    {
	    $data = ['dni' => '', 'nif' => '', 'nifCif' => '', 'fecha' => '', 'baseImponible' => '', 'total' => '', 'iva' => [], 'porcentajeIva' => '', 'nombre' => '', 'concepto' => '', 'empresa' => '', 'tipo' => 'Recibidas'];

	    $repoFacturas = $this->entityManager->getRepository(Facturas::class);
	    $file = $request->files->get('pdf');
	    $data['nombre'] = $request->get('nombre');
	    $data['empresa'] = $request->get('empresa');
	    $data['concepto'] = $request->get('concepto');
	    $data['nif'] = $request->get('nif');
	    $data['fecha'] = $this->stringToDate($request->get('fecha'));
	    $data['baseImponible'] = $request->get('baseImponible');
	    $data['total'] = $request->get('total');
	    $data['iva'] = json_decode($request->get('impuestos'));
	    $projectDir = $this->getParameter('kernel.project_dir');

	    if ($file && $file->isValid()) {
           	$originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            	$safeFilename = $slugger->slug($originalFilename);
	    	$newFilename = '/pdf/' . $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
            	try {
			$file->move($this->getParameter('Pdf'), $newFilename);
	
			$factura = new Facturas();
			$factura->setNombre($data['nombre']);
			$factura->setConcepto($data['concepto']);

			if (count($data['iva']) > 0) {
				foreach ($data['iva'] as $mercancia) {
					$impuesto = new ImpuestoFacturas();
					$impuesto->setFactura($factura);
					$impuesto->setImpuesto($mercancia->impuesto);
					$impuesto->setCantidad($mercancia->cantidad);
					$this->entityManager->persist($impuesto);
					$factura->addImpuestoFactura($impuesto);
				}
			}
			$factura->setEmpresa($data['empresa']);
			$factura->setImporte($data['total']);
			$factura->setRutaPdf($newFilename);
			$factura->setTipo($data['tipo']);
			$factura->setFechaFactura($data['fecha']);

			$this->entityManager->persist($factura);
			$this->entityManager->flush();

		} catch (FileException $e) {
                   return new JsonResponse(['status' => 'error', 'message' => 'Failed to upload file'], 500);
	    	}
	    }
	    return new JsonResponse(['status' => 'success', 'message' => 'File uploaded successfully']);
    }


    /**
     * @Route("/uploadpdfs", name="upload_pdfs")
     */
    public function uploadPdfs (Request $request, SluggerInterface $slugger): Response
    {
	$data = ['dni' => '', 'nif' => '', 'nifCif' => '', 'fecha' => '', 'baseImponible' => '', 'total' => '', 'iva' => [], 'porcentajeIva' => '', 'nombre' => '', 'concepto' => '', 'empresa' => '', 'tipo' => 'Recibidas'];

	$repoFacturas = $this->entityManager->getRepository(Facturas::class);
	$file = $request->files->get('pdf');
	$projectDir = $this->getParameter('kernel.project_dir');

	$mindeeClient = new Client($_ENV['MINDEE_API_KEY']);

        if ($file && $file->isValid()) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
	    $newFilename = '/pdf/' . $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('Pdf'),
                    $newFilename
		);
		
		$inputSource = $mindeeClient->sourceFromPath($projectDir . '/public' . $newFilename);
		$apiResponse = $mindeeClient->parse(InvoiceV4::class, $inputSource);

		// var_dump($apiResponse);
		// die;
		
		$data['nombre'] = $apiResponse->document->inference->prediction->supplierName->value;
		$data['nif'] = $apiResponse->document->inference->prediction->supplierCompanyRegistrations[0]->value;
		$data['fecha'] = $apiResponse->document->inference->prediction->date->value;
		$data['fecha'] = $this->stringToDate($data['fecha']);
		$data['baseImponible'] = $apiResponse->document->inference->prediction->totalNet->value;
		$data['total'] = $apiResponse->document->inference->prediction->totalAmount->value;
		$impuestos = $apiResponse->document->inference->prediction->taxes;
		$data['empresa'] = $apiResponse->document->inference->prediction->supplierName->value;
		$data['concepto'] = $apiResponse->document->inference->prediction->supplierName->value;

		foreach ($impuestos as $impuesto) {
			$imp = $impuesto->rate;
			$cant = $impuesto->value;
			array_push($data['iva'], ['impuesto' => $imp, 'cantidad' => $cant]);
		}

		$factura = new Facturas();
		$factura->setNombre($data['nombre']);
		$factura->setConcepto($data['concepto']);
	
		if (count($data['iva']) > 0) {
			foreach ($data['iva'] as $mercancia) {
				$impuesto = new ImpuestoFacturas();
				$impuesto->setFactura($factura);
				$impuesto->setImpuesto($mercancia['impuesto']);
				$impuesto->setCantidad($mercancia['cantidad']);
				$this->entityManager->persist($impuesto);
				$factura->addImpuestoFactura($impuesto);
			}
		}

		$factura->setEmpresa($data['empresa']);
		$factura->setImporte($data['total']);
		$factura->setRutaPdf($newFilename);
		$factura->setTipo($data['tipo']);
		$factura->setFechaFactura($data['fecha']);


		$this->entityManager->persist($factura);
		$this->entityManager->flush();

            } catch (FileException $e) {
                return new JsonResponse(['status' => 'error', 'message' => 'Failed to upload file'], 500);
            }

            return new JsonResponse(['status' => 'success', 'message' => 'File uploaded successfully']);
        }

        return new JsonResponse(['status' => 'error', 'message' => 'Invalid file upload'], 400);
    }



    private function stringToDate($str) {
	$formats = ['d/m/Y','Y-m-d','m-d-Y','d-m-Y','Y/m/d','d M Y','M d, Y','d.m.Y','Y.m.d','Y-m-d H:i:s'];
	foreach ($formats as $format) {
		$date = \DateTime::createFromFormat($format, $str);
		$errors = \DateTime::getLastErrors();
		if ($date && is_array($errors) && !$errors['warning_count'] && !$errors['error_count']) {
			return $date;
		}
	}
	return false;
    }

}
