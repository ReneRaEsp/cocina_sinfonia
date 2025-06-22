<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;
use App\Entity\Proveedores;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;


class ProveedoresController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/proveedores", name="proveedores")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $proveedores = $em->getRepository(Proveedores::class)->findAll();

        $data = array();
        foreach($proveedores as $proveedor){
            $temp = array(
                'id' => $proveedor->getId(),
                'name' => $proveedor->getName(),
                'dir' => $proveedor->getDir(),
                'email' => $proveedor->getEmail(),
                'telf' => $proveedor->getTelf(),
                'nif' => $proveedor->getNIF(),
                );
                array_push($data, $temp);
        }
        
        $new_provider = New Proveedores();
        //Creamos en formulario para añadir un proveedor
        $form = $this->createFormBuilder($new_provider)
            ->add('name', TextType::class, array('label' => 'Nombre'))
            ->add('dir', TextType::class, array('label' => 'Dirección'))
            ->add('email', EmailType::class, array('label' => 'Correo electrónico'))
            ->add('telf', NumberType::class, array('label' => 'Teléfono'))
            ->add('nif', TextType::class, array('label' => 'NIF/CIF'))
            ->add('submit', SubmitType::class, array('label' => 'Crear proveedor'))
            ->getForm();

            //Comprobamos si el formulario  ha sido enviado  y en ese caso guardamos el objeto en la bbdd
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $new_provider_1 = New Proveedores();
                // guardar el objeto en la base de datos
                $new_provider_1->setName($form['name']->getData());
                $new_provider_1->setDir($form['dir']->getData());
                $new_provider_1->setEmail($form['email']->getData());
                $new_provider_1->setTelf($form['telf']->getData());
                $new_provider_1->setNIF($form['nif']->getData());

                //Con estas dos llamadas guardamos y actualizamos la base de datos
                $em->persist($new_provider_1);
                $em->flush();

                // Crea un nuevo objeto vacío y asignarlo al formulario
                $form = $this->createFormBuilder(new Proveedores())
                    ->add('name', TextType::class, array('label' => 'Nombre'))
                    ->add('dir', TextType::class, array('label' => 'Dirección'))
                    ->add('email', EmailType::class, array('label' => 'Correo electrónico'))
                    ->add('telf', NumberType::class, array('label' => 'Teléfono'))
                    ->add('nif', TextType::class, array('label' => 'NIF/CIF'))
                    ->add('submit', SubmitType::class, array('label' => 'Crear proveedor'))
                    ->getForm();
                    $em = $this->getDoctrine()->getManager();
                    $proveedores_2 = $em->getRepository(Proveedores::class)->findAll();

                    $data_2 = array();
                    foreach($proveedores_2 as $proveedor){
                        $temp_2 = array(
                            'id' => $proveedor->getId(),
                            'name' => $proveedor->getName(),
                            'dir' => $proveedor->getDir(),
                            'email' => $proveedor->getEmail(),
                            'telf' => $proveedor->getTelf(),
                            'nif' => $proveedor->getNIF(),
                            );
                            array_push($data_2, $temp_2);
                    }
    
                return $this->render('proveedores/index.html.twig', [
                    'controller_name' => 'ProveedoresController',
                    'proveedores' => $data_2,
                    'form' => $form->createView(),
                    'provider_added' => true
                ]);
            }

        return $this->render('proveedores/index.html.twig', [
            'controller_name' => 'ProveedoresController',
            'proveedores' => $data,
            'form' => $form->createView(),
            'provider_added' => false
        ]);
    }
    
    /**
        * @Route("/proveedores_set", name="proveedores_set", methods={"POST"})
    */
    
    public function setProveedor(Request $request)
    {
        $id = $request->request->get('id');
        $value = $request->request->get('value');
        $field = $request->request->get('colum'); // asegurarse que esta variable esta definida

        $repoProveedor = $this->entityManager->getRepository(Proveedores::class);
        $proveedor = $repoProveedor->findOneBy(['id' => $id]);

        //$stock->setName($value);
        switch ($field) {
            case 'name':
                $proveedor->setName($value);
                break;
            case 'dir':
                $proveedor->setDir($value);
                break;
            case 'email':
                $proveedor->setEmail($value);
                break;
            case 'telf':
                $proveedor->setTelf($value);
                break;
            default:
                return new JsonResponse([
                    'error' => 'Campo no válido',
                ]);
        }

        $this->entityManager->persist($proveedor);
        $this->entityManager->flush();


        return new JsonResponse([
            'proveedor' => 'El proveedor se ha actualizado correctamente',

        ]);
    }


    /**
        * @Route("/updateproveedor", name="updateproveedor", methods={"POST"})
    */
    
    public function updateProveedor(Request $request)
    {
	$data = json_decode($request->getContent(), true);
	$em = $this->getDoctrine()->getManager();
	$repoProveedor = $em->getRepository(Proveedores::class);
	// Obtener los datos del JSON
	$id = $data['id'];
	$name = $data['name'];
	$dir = $data['dir'];
	$email = $data['email'];
	$telf = $data['telf'];
	$nif = $data['nif'];

	// Obtener la entidad Proveedor existente
	$proveedor = $repoProveedor->findOneBy(['id' => $id]);
        // Verificar si el Proveedor existe
	if (!$proveedor) {
		return $this->json(['error' => 'Proveedor no encontrado'], 404);
	}
	$proveedor->setName($name);
	$proveedor->setDir($dir);
	$proveedor->setEmail($email);
	$proveedor->setTelf($telf);
	$proveedor->setNIF($nif);
	//Persistir y guardar en la base de datos
	$em->persist($proveedor);
	$em->flush();

	return $this->json(['proveedor' => 'El proveedor se ha actualizado correctamente']);
    }



   /**
        * @Route("/proveedores_list", name="proveedores_list", methods={"GET"})
    */
    public function listProveedores(Request $request)
    {
	$data = $request->query->get('itemId');
        return $this->json(['message' => $data]);
    }


    
}
