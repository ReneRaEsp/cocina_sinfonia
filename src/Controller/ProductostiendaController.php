<?php

namespace App\Controller;

use App\Entity\Productostienda;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductostiendaRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;


class ProductostiendaController extends AbstractController
{

    private $entityManager;
    private $security;
    private $tiendaRepository;

    public function __construct(EntityManagerInterface $entityManager, Security $security, ProductostiendaRepository $tiendaRepository)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->tiendaRepository = $tiendaRepository;
    }
    /**
     * @Route("/productostienda", name="productostienda")
     */
    public function index(Request $request): Response
    {

        $productos = $this->tiendaRepository->findAll();

        $data = array();
        foreach ($productos as $producto) {
                $temp = array(
                    'id' => $producto->getId(),
                    'name' => $producto->getNombre(),
                    'pvp' => $producto->getPvp(),
                );

                array_push($data, $temp);
    
            
        }

        $new_product = New Productostienda();
        //Creamos en formulario para añadir un proveedor
        $form = $this->createFormBuilder($new_product)
            ->add('nombre', TextType::class, array('label' => 'Nombre'))
            ->add('pvp', NumberType::class, array('label' => 'Pvp'))
            ->add('submit', SubmitType::class, array('label' => 'Añadir producto'))
            ->getForm();

            //Comprobamos si el formulario  ha sido enviado  y en ese caso guardamos el objeto en la bbdd
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $new_product_1 = New Productostienda();
                // guardar el objeto en la base de datos
                $new_product_1->setNombre($form['nombre']->getData());
                $new_product_1->setPvp($form['pvp']->getData());

                //Con estas dos llamadas guardamos y actualizamos la base de datos
                $this->entityManager->persist($new_product_1);
                $this->entityManager->flush();

                // Crea un nuevo objeto vacío y asignarlo al formulario
                $form = $this->createFormBuilder(new Productostienda())
                ->add('nombre', TextType::class, array('label' => 'Nombre'))
                ->add('pvp', NumberType::class, array('label' => 'Pvp'))
                ->add('submit', SubmitType::class, array('label' => 'Añadir producto'))
                ->getForm();
                    $productos_2 = $this->entityManager->getRepository(Productostienda::class)->findAll();

                    $data_2 = array();
                    foreach($productos_2 as $producto) {
                        $temp_2 = array(
                            'id' => $producto->getId(),
                            'name' => $producto->getNombre(),
                            'pvp' => $producto->getPvp(),
                        );
                            array_push($data_2, $temp_2);
                    }
    
                return $this->render('productostienda/index.html.twig', [
                    'controller_name' => 'ProductostiendaController',
                    'productos' => $data_2,
                    'form' => $form->createView(),
                ]);
            }


        return $this->render('productostienda/index.html.twig', [
            'controller_name' => 'ProductostiendaController',
            'productos' => $data,
            'form' => $form->createView(),

        ]);
    }

     /**
     * * @Route("/updatetienda", name="update_tienda", methods={"POST"})
     * */
    public function updatetienda(Request $request) 
    {
      $data = json_decode($request->getContent(), true);

      // Obtener los datos del JSON
      $id = $data['id'];
      $name = $data['name'];
      $pvp = $data['pvp'];

      $productotienda = $this->tiendaRepository->findOneBy(['id' => $id]);

      $productotienda->setNombre($name);
      $productotienda->setPvp($pvp);



       // Persistir y guardar en la base de datos
       $this->entityManager->persist($productotienda);
       $this->entityManager->flush();

       return $this->json(['message' => 'Producto actualizado correctamente']);
    }
}
