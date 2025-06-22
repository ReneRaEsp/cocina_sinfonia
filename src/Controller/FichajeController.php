<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Fichaje;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Security;

class FichajeController extends AbstractController
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    /**
     * @Route("/fichaje", name="fichaje")
     */
    public function index(): Response
    {

        $token = $this->security->getToken();

        // Verificar si hay un token y si el usuario está autenticado
        if ($token && $token->isAuthenticated()) {
            // Obtener el objeto de usuario actual
            $user = $token->getUser();

            // Verificar si el usuario es un objeto de usuario (puede ser una cadena en algunos casos)
            if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                // Obtener el nombre de usuario
                $username = $user->getUserIdentifier();
            }
        }

        $hoy = new DateTime();
        $repoFichaje = $this->entityManager->getRepository(Fichaje::class);
	$dateFichaje = $repoFichaje->findOneBy(['fecha' => $hoy, 'user' => $username]);
	$fichajes = $repoFichaje->findAll();
	$fichajesUser = $repoFichaje->findBy(['user' => $username]);

	$dataFichajes = array();
	$dataFichajesUser = array();

	foreach ($fichajes as $fichaje) {
		$temp = array(
		'id' => $fichaje->getId(), 
		'user' => $fichaje->getUser(), 
		'fecha' => $fichaje->getFecha(), 
		'inicio_am' => $fichaje->getInicioAm(), 
		'fin_am' => $fichaje->getFinAm(), 
		'inicio_pm' => $fichaje->getInicioPm(), 
		'fin_pm' => $fichaje->getFinPm());

		array_push($dataFichajes, $temp);
	}
	
	foreach ($fichajesUser as $fichaje) {
		$temp = array(
		'id' => $fichaje->getId(), 
		'user' => $fichaje->getUser(), 
		'fecha' => $fichaje->getFecha(), 
		'inicio_am' => $fichaje->getInicioAm(), 
		'fin_am' => $fichaje->getFinAm(), 
		'inicio_pm' => $fichaje->getInicioPm(), 
		'fin_pm' => $fichaje->getFinPm());

		array_push($dataFichajesUser, $temp);
	}

        $fichajeMannana = ($dateFichaje && $dateFichaje->getInicioAm() !== null) ? true : false;
        $fichajeTarde = ($dateFichaje && $dateFichaje->getInicioPm() !== null) ? true : false;

        $horaInicioAm = ($dateFichaje && $dateFichaje->getInicioAm() !== null) ? $dateFichaje->getInicioAm() : false;
        $horaFinAm = ($dateFichaje && $dateFichaje->getFinAm() !== null) ? $dateFichaje->getFinAm() : false;
        $horaInicioPm = ($dateFichaje && $dateFichaje->getInicioPm() !== null) ? $dateFichaje->getInicioPm() : false;
        $horaFinPm = ($dateFichaje && $dateFichaje->getFinPm() !== null) ? $dateFichaje->getFinPm() : false;

        $horaInicioAmString = $horaInicioAm !== false ? $horaInicioAm->format('H:i') : '';
        $horaFinAmString = $horaFinAm !== false  ? $horaFinAm->format('H:i') : '';
        $horaInicioPmString = $horaInicioPm !== false ? $horaInicioPm->format('H:i') : '';
        $horaFinPmString = $horaFinPm !== false ? $horaFinPm->format('H:i') : '';

        if ($fichajeMannana === true && $fichajeTarde === true) {

            $this->addFlash('info', '¡No puedes modificar tu fichaje una vez insertadas las horas!');
        }



        return $this->render('fichaje/index.html.twig', [
            'controller_name' => 'FichajeController',
            'fichajeMannana' => $fichajeMannana,
            'fichajeTarde' => $fichajeTarde,
            'horaInicioAm' => $horaInicioAmString,
            'horaFinAm' => $horaFinAmString,
            'horaInicioPm' => $horaInicioPmString,
	    'horaFinPm' => $horaFinPmString,
	    'fichajes' => $dataFichajes,
	    'fichajesUser' => $dataFichajesUser
        ]);
    }

    /**
     * @Route("/insertfichaje", name="insert_fichaje")
     */
    public function insertFichaje(Request $request)
    {
        $inicioMannana = $request->request->get('inicioMannana');
        $finMannana = $request->request->get('finMannana');
        $inicioTarde = $request->request->get('inicioTarde');
	$finTarde = $request->request->get('finTarde');

        $fecha = new DateTime();
        // Obtener el token de seguridad
        $token = $this->security->getToken();

        // Verificar si hay un token y si el usuario está autenticado
        if ($token && $token->isAuthenticated()) {
            // Obtener el objeto de usuario actual
            $user = $token->getUser();

            // Verificar si el usuario es un objeto de usuario (puede ser una cadena en algunos casos)
            if ($user instanceof \Symfony\Component\Security\Core\User\UserInterface) {
                // Obtener el nombre de usuario
                $username = $user->getUserIdentifier();
            }
        }


        $repoFichaje = $this->entityManager->getRepository(Fichaje::class);
        $dateFichaje = $repoFichaje->findOneBy(['fecha' => $fecha, 'user' => $username]);



        if ($dateFichaje) {
            if ($dateFichaje->getInicioAm() !== null || $dateFichaje->getFinAm() !== null) {

                // $dateTimeInicioAm = DateTime::createFromFormat('H:i:s', $inicioTarde)

                $dateFichaje->setInicioPm(new DateTimeImmutable('today ' . $inicioTarde));
                $dateFichaje->setFinPm(new DateTimeImmutable('today ' . $finTarde));
            } else {

                $dateFichaje->setInicioAm(new DateTimeImmutable('today ' . $inicioMannana));
                $dateFichaje->setFinAm(new DateTimeImmutable('today ' . $finMannana));
            }

            $this->entityManager->persist($dateFichaje);
        } else {
            $fichaje = new Fichaje();


	    if (!empty($inicioMannana) && !empty($finMannana) && !empty($inicioTarde) && !empty($finTarde)) {
		$fichaje->setInicioAm(new DateTimeImmutable('today ' . $inicioMannana));
                $fichaje->setFinAm(new DateTimeImmutable('today ' . $finMannana));
                $fichaje->setInicioPm(new DateTimeImmutable('today ' . $inicioTarde));
                $fichaje->setFinPm(new DateTimeImmutable('today ' . $finTarde));
	    } else {
		 if (!empty($inicioMannana) && !empty($finMannana)) {

                	$fichaje->setInicioAm(new DateTimeImmutable('today ' . $inicioMannana));
                	$fichaje->setFinAm(new DateTimeImmutable('today ' . $finMannana));
            	} else if (!empty($inicioTarde) && !empty($finTarde)) {

                	$fichaje->setInicioPm(new DateTimeImmutable('today ' . $inicioTarde));
               		$fichaje->setFinPm(new DateTimeImmutable('today ' . $finTarde));
	    	} 

	    }


           
            $fichaje->setUser($username);
	    $fichaje->setFecha($fecha);

            $this->entityManager->persist($fichaje);
        }



        $this->entityManager->flush();

        return new JsonResponse([]);
    }
}
