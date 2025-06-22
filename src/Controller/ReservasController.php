<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservas;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ReservasController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/reservas", name="reservas")
     */
    public function index(Request $request): Response
    {
        $repoReservas = $this->entityManager->getRepository(Reservas::class);
        $reservas = $repoReservas->findAll();

        $data_reservas = array();

        foreach($reservas as $reserva){
            $temp = array(
                'id' => $reserva->getId(),
                'title' => $reserva->getNombre(),
                'start' => $reserva->getFecha()->format('Y-m-d\TH:i'),
                'tipo' => $reserva->getTipo(),
                'allDay' => false,
                'extendedProps' => array(
                    'comensales' => $reserva->getComensales(),
                    'hora' => $reserva->getFecha()->format('H:i'),
                    'telefono' => $reserva->getTelefono(),
                )
            );

            array_push($data_reservas, $temp);

        };

        $json_reservas = json_encode($data_reservas);

        $new_reserva = New Reservas();
        //Creamos en formulario para añadir un proveedor
        $form = $this->createFormBuilder($new_reserva)
            ->add('nombre', TextType::class, array('label' => 'Nombre'))
            ->add('fecha', DateTimeType::class, array(
                'label' => 'Fecha y hora',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm:ss',
                'html5' => false,
                'attr' => array('class' => 'flatpickr'),
            ))
            ->add('tipo', ChoiceType::class, array(
                'label' => 'Comida o cena',
                'choices' => array(
                    'Comida' => 'comida',
                    'Cena' => 'cena',
                ),
                'placeholder' => 'Selecciona una opción',
            ))      
            ->add('telefono', NumberType::class, array('label' => 'Teléfono'))
            ->add('comensales', NumberType::class, array('label' => 'Comensales'))
            ->add('submit', SubmitType::class, array('label' => 'Crear reserva'))
            ->getForm();

            //Comprobamos si el formulario  ha sido enviado  y en ese caso guardamos el objeto en la bbdd
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $new_reserva_1 = New Reservas();
                // guardar el objeto en la base de datos
                $new_reserva_1->setNombre($form['nombre']->getData());
                $new_reserva_1->setFecha($form['fecha']->getData());
                $new_reserva_1->setTelefono($form['telefono']->getData());
                $new_reserva_1->setTipo($form['tipo']->getData());
                $new_reserva_1->setComensales($form['comensales']->getData());

                //Con estas dos llamadas guardamos y actualizamos la base de datos
                $this->entityManager->persist($new_reserva_1);
                $this->entityManager->flush();

                // Crea un nuevo objeto vacío y asignarlo al formulario
                $form = $this->createFormBuilder(new Reservas())
                    ->add('nombre', TextType::class, array('label' => 'Nombre'))
                    ->add('fecha', DateTimeType::class, array(
                        'label' => 'Fecha y hora',
                        'widget' => 'single_text',
                        'format' => 'yyyy-MM-dd HH:mm:ss',
                        'html5' => false,
                        'attr' => array('class' => 'flatpickr'),
                    ))
                    ->add('tipo', ChoiceType::class, array(
                        'label' => 'Comida o cena',
                        'choices' => array(
                            'Comida' => 'comida',
                            'Cena' => 'cena',
                        ),
                        'placeholder' => 'Selecciona una opción',
                    ))      
                    ->add('telefono', NumberType::class, array('label' => 'Teléfono'))
                    ->add('comensales', NumberType::class, array('label' => 'Comensales'))
                    ->add('submit', SubmitType::class, array('label' => 'Crear reserva'))
                    ->getForm();

                    $repoReservas = $this->entityManager->getRepository(Reservas::class);
                    $reservas_1 = $repoReservas->findAll();
                    
                    $data_reservas_1 = array();
                    foreach($reservas_1 as $reserva){
                        $temp = array(
                            'id' => $reserva->getId(),
                            'title' => $reserva->getNombre(),
                            'start' => $reserva->getFecha()->format('Y-m-d\TH:i'),
                            'tipo' => $reserva->getTipo(),
                            'allDay' => false,
                            'extendedProps' => array(
                                'comensales' => $reserva->getComensales(),
                                'hora' => $reserva->getFecha()->format('H:i'),
                                'telefono' => $reserva->getTelefono(),
                            )
                        );
            
                        array_push($data_reservas_1, $temp);
            
                    };
            
                    $json_reservas = json_encode($data_reservas_1);
    
                return $this->render('reservas/index.html.twig', [
                    'reservas' => $json_reservas,
                    'booking_added' => true,
                    'form' => $form->createView(),
                ]);
            }


        return $this->render('reservas/index.html.twig', [
            'booking_added' => false,
            'reservas' => $json_reservas,
            'form' => $form->createView(),
        ]);
    }

/**
 * @Route("/eliminar-reserva/{id}", name="eliminar_reserva")
 */
public function eliminarReserva($id, EntityManagerInterface $entityManager)
{
    $reserva = $entityManager->getRepository(Reservas::class)->find($id);
    
    if (!$reserva) {
        throw $this->createNotFoundException(
            'No se ha encontrado la reserva con el ID '.$id
        );
    }
    
    // eliminar la reserva
    $entityManager->remove($reserva);
    $entityManager->flush();
    
    // redirigir a la página de reservas
    return $this->redirectToRoute('reservas');
}
}
