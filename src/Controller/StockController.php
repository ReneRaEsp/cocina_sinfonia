<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Stock;
use App\Entity\Proveedores;
use App\Entity\TipoComida;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class StockController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/stock", name="stock")
     */
    public function index(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $stocks = $em->getRepository(Stock::class)->findAll();

        $data = array();
        foreach ($stocks as $stock) {
                $found = false;
                $temp = array(
                    'id' => $stock->getId(),
                    'name' => $stock->getName(),
                    'desc' => $stock->getDescription(),
                    'amount' => $stock->getAmount(),
                    // 'p_id' => $stock->getProvider() ? $stock->getProvider()->getName() : '',
                    'tf_id' => $stock->getTypeFood() ? $stock->getTypeFood()->getName() : '',
                );
        
                // Buscar si ya existe un elemento con el mismo nombre en $data
                foreach ($data as &$existingItem) {
                    if ($existingItem['name'] === $temp['name']) {
                        // Si existe, suma la cantidad al elemento existente
                        $existingItem['amount'] += $temp['amount'];
                        $found = true;
                        break;
                    }
                }
        
                // Si no se encontró un elemento existente, agrega uno nuevo
                if (!$found) {
                    array_push($data, $temp);
                }
            
        }
        
        // Limpia las referencias creadas por el bucle interno
        unset($existingItem);
        // foreach ($stocks as $stock) {
            
        // if($stock->getComida()->isUnitario()){    
        //     $temp = array(
        //         'id' => $stock->getId(),
        //         'name' => $stock->getName(),
        //         'desc' => $stock->getDescription(),
        //         'amount' => $stock->getAmount(),
        //         'p_id' => $stock->getProvider()->getName(),
        //         'tf_id' => $stock->getTypeFood()->getName(),
        //     );
        //     array_push($data, $temp);
        //     }
        // }

        //Creamos en formulario para añadir un proveedor
        $form = $this->createFormBuilder(new Stock())
            ->add('name', TextType::class, array('label' => 'Nombre'))
            ->add('description', TextType::class, array('label' => 'Descripción'))
            ->add('amount', NumberType::class, array('label' => 'Cantidad'))
            // ->add('type_food', ChoiceType::class, [
            //     'choices' => $this->getTypeFoods(),
            //     'choice_label' => function ($type_food) {
            //         return $type_food->getName();
            //     },
            //     'label' => 'Tipo de comida',
            //     'placeholder' => 'Selecciona un tipo',
            // ])
            ->add('submit', SubmitType::class, array('label' => 'Añadir producto'))
            ->getForm();

        //Comprobamos si el formulario  ha sido enviado  y en ese caso guardamos el objeto en la bbdd
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $new_stock1 = new Stock();
            // guardar el objeto en la base de datos
            $new_stock1->setName($form['name']->getData());
            $new_stock1->setDescription($form['description']->getData());
            $new_stock1->setAmount($form['amount']->getData());
            // $provider_form = $em->getRepository(Proveedores::class)->findOneBy(['name' => $form['provider']->getData()]);

            // $provider = $form['provider']->getData();
            // if ($provider) {
            //     $new_stock1->setProvider($provider);
            // }
            $tf = $form['type_food']->getData();
            // $tf_form = $em->getRepository(TipoComida::class)->findOneBy(['name' => $form['type_food']->getData()]);
            if ($tf) {
                $new_stock1->setTypeFood($tf);
            }

            // Con estas dos llamadas guardamos y actualizamos la base de datos
            $em->persist($new_stock1);
            $em->flush();

            // Crea un nuevo objeto vacío y asignarlo al formulario
            $form = $this->createFormBuilder(new Stock())
                ->add('name', TextType::class, array('label' => 'Nombre'))
                ->add('description', TextType::class, array('label' => 'Descripción'))
                ->add('amount', NumberType::class, array('label' => 'Cantidad'))
                // ->add('type_food', ChoiceType::class, [
                //     'choices' => $this->getTypeFoods(),
                //     'choice_label' => function ($type_food) {
                //         return $type_food->getName();
                //     },
                //     'label' => 'Tipo de comida',
                //     'placeholder' => 'Selecciona un tipo',
                // ])
                ->add('submit', SubmitType::class, array('label' => 'Añadir producto'))
                ->getForm();


            $em = $this->getDoctrine()->getManager();
            $stocks_2 = $em->getRepository(Stock::class)->findAll();

            $data_2 = array();
            foreach ($stocks_2 as $stock_2) {
                $temp_2 = array(
                    'id' => $stock_2->getId(),
                    'name' => $stock_2->getName(),
                    'desc' => $stock_2->getDescription(),
                    'amount' => $stock_2->getAmount(),
                    'p_id' => $stock_2->getProvider()->getName(),
		    'tf_id' => $stock_2->getTypeFood()->getName()	
                );
                array_push($data_2, $temp_2);
            }

            $proveedores = $em->getRepository(Proveedores::class)->findAll();
            $proveedoresData_2 = [];
            foreach ($proveedores as $proveedor) {
                $proveedoresData_2[] = [
                    'id' => $proveedor->getId(),
                    'name' => $proveedor->getName(),
                    // Agrega aquí cualquier otro dato que quieras incluir en el array de opciones
                ];
            }

            $tipos_comida = $em->getRepository(TipoComida::class)->findAll();
            $tipoData_2 = [];
            foreach ($tipos_comida as $tc) {
                $tipoData_2[] = [
                    'id' => $tc->getId(),
                    'name' => $tc->getName(),
                    // Agrega aquí cualquier otro dato que quieras incluir en el array de opciones
                ];
            }

            return $this->render('stock/index.html.twig', [
                'controller_name' => 'StockController',
                'stock' => $data_2,
                'form' => $form->createView(),
                'stock_added' => true,
                'proveedores' => $proveedoresData_2,
		'tipos' => $tipoData_2,
            ]);
        }

        $proveedores = $em->getRepository(Proveedores::class)->findAll();
        $proveedoresData = [];
        foreach ($proveedores as $proveedor) {
            $proveedoresData[] = [
                'id' => $proveedor->getId(),
                'name' => $proveedor->getName(),
                // Agrega aquí cualquier otro dato que quieras incluir en el array de opciones
            ];
        }
        $tipos_comida = $em->getRepository(TipoComida::class)->findAll();
        $tipoData = [];
        foreach ($tipos_comida as $tc) {
            $tipoData[] = [
                'id' => $tc->getId(),
                'name' => $tc->getName(),
                // Agrega aquí cualquier otro dato que quieras incluir en el array de opciones
            ];
        }

        return $this->render('stock/index.html.twig', [
            'controller_name' => 'StockController',
            'stock' => $data,
            'stock_added' => false,
            'form' => $form->createView(),
            'proveedores' => $proveedoresData,
	    'tipos' => $tipoData,
        ]);
    }

    private function getProviders()
    {
        $em = $this->getDoctrine()->getManager();
        // Carga todos los proveedores disponibles en la base de datos.
        return  $em->getRepository(Proveedores::class)->findAll();
    }
    private function getTypeFoods()
    {
        $em = $this->getDoctrine()->getManager();
        // Carga todos los Tipo Comida disponibles en la base de datos.
        return $em->getRepository(TipoComida::class)->findAll();
    }

    /**
     * @Route("/setstock", name="set_stock", methods={"POST"})
     */
    public function setStock(Request $request)
    {
        $id = $request->request->get('id');
        $value = $request->request->get('value');
        $repoStock = $this->entityManager->getRepository(Stock::class);
        $repoProvider = $this->entityManager->getRepository(Proveedores::class);
        $repoTipoComida = $this->entityManager->getRepository(TipoComida::class);
        $stock = $repoStock->findOneBy(['id' => $id]);


        $column = $request->request->get('column'); // Este sería el nombre de la columna recibido en el JSON

        switch ($column) {
            case 'nombre':
                // Código para procesar la columna nombre
                $stock->setName($value);
                break;
            case 'proveedor':
                // Código para procesar la columna proveedor
                $provider = $repoProvider->findOneBy(['id' => $value]);
                $stock->setProvider($provider);
                break;
            case 'tipo':
                // Código para procesar la columna tipo
                $tipo_c = $repoTipoComida->findOneBy(['id' => $value]);
                $stock->setTypeFood($tipo_c);
                break;
            case 'desc':
                // Código para procesar la columna descripción
                $stock->setDescription($value);
                break;
            case 'cantidad':
                // Código para procesar la columna descripción
                $stock->setAmount($value);
                // $stocks = $repoStock->findAll();
                // $data_stocks = array();
                // foreach ($stocks as $stock) {
                //     $temp_2 = array(
                //         'id' => $stock->getId(),
                //         'name' => $stock->getName(),
                //         'desc' => $stock->getDescription(),
                //         'amount' => $stock->getAmount(),
                //         'p_id' => $stock->getProvider()->getName(),
                //         'tf_id' => $stock->getTypeFood()->getName(),
                //     );
                //     array_push($data_stocks, $temp_2);
                // }
                // return new JsonResponse([
                //     'stocks' => $data_stocks,
                //     'texto' => 'Producto actualizado',

                // ], 200);
                break;
            default:
                // Código en caso de que no se reconozca el nombre de la columna recibido
                return new JsonResponse(['error' => 'Columna no válida: ' . $column], 400);
                break;
        }

        $this->entityManager->persist($stock);
        $this->entityManager->flush();


        return new JsonResponse([
            'texto' => 'Producto actualizado',

        ], 200);
    }


    /**
     * @Route("/addstock", name="add_stock", methods={"POST"})
     */
    public function addStock(Request $request) 
    {
      $data = json_decode($request->getContent(), true);
      $em = $this->getDoctrine()->getManager();
      $repoStock = $em->getRepository(Stock::class);

      // Obtén los datos del JSON
      $name = $data['name'];
      $description = $data['description'];
      $providerId = $data['provider'];
      $amount = $data['amount'];
      // Obtén las entidades relacionadas
    //   $provider = $em->getRepository(Proveedores::class)->findOneBy(['name' => $providerId]);
      $typeFood = $em->getRepository(TipoComida::class)->findOneBy(['name' => $description]);
      // Asigna los nuevos valores al objeto Stock existente
      $stock = new Stock();
      $stock->setName($name);
      $stock->setDescription($description);
      $stock->setAmount($amount);
    //   $stock->setProvider($provider);
      $stock->setTypeFood($typeFood);
      // Persiste y guarda en la base de datos
      $em->persist($stock);
      $em->flush();
      
      return $this->json(['message' => 'Producto agregado correctamente']);
    }
    
    /**
     * * @Route("/updatestock", name="update_stock", methods={"POST"})
     * */
    public function updateStock(Request $request) 
    {
      $data = json_decode($request->getContent(), true);
      $em = $this->getDoctrine()->getManager();
      $repoStock = $em->getRepository(Stock::class);

      // Obtener los datos del JSON
      $id = $data['id'];
      $name = $data['name'];
      $description = $data['description'];
      $providerId = $data['provider'];
      $amount = $data['amount'];

       // Obtener la entidad Stock existente
       $stock = $repoStock->findOneBy(['id' => $id]);
       // Verifica si el Stock existe
       if (!$stock) {
          return $this->json(['error' => 'Producto no encontrado'], 404);
       }
       // Obtener las entidades relacionadas
       $provider = $em->getRepository(Proveedores::class)->findOneBy(['name' => $providerId]);
       $typeFood = $em->getRepository(TipoComida::class)->findOneBy(['name' => $description]);
       // Asignar los nuevos valores al objeto Stock existente
       $stock->setName($name);
       $stock->setDescription($description);
       $stock->setAmount($amount);
       $stock->setProvider($provider);
       $stock->setTypeFood($typeFood);

       // Persistir y guardar en la base de datos
       $em->persist($stock);
       $em->flush();

       return $this->json(['message' => 'Producto actualizado correctamente']);
    }


}
