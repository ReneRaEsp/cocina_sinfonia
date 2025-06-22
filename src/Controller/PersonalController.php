<?php

namespace App\Controller;

use App\Entity\RegistroUsuarios;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class PersonalController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/personal", name="personal")
     */
    public function index( UserManagerInterface $userManager): Response
    {
        $repoRegistros = $this->entityManager->getRepository(RegistroUsuarios::class);

        $registros = $repoRegistros->findAll();


        $newRegister = array();
        foreach ($registros as $register) {
            $lastLogin = $register->getFecha();
            $date = $lastLogin ? $lastLogin->format('d-m-Y H:i:s') : '';

            $newRegister[] = array(
                'username' => $register->getName(),
                'email' => $register->getEmail(),
                'lastLogin' => $date,
                // 'roles' => $register->getRoles()
            );
        }

        $users = $userManager->findUsers();


        $newUsers = array();
        foreach ($users as $user) {
            $lastLogin = $user->getLastLogin();
            if ($lastLogin instanceof DateTime) {
                $lastLogin->modify('+1 hour'); // Si es un objeto DateTime, añadir una hora
                $date = $lastLogin->format('d-m-Y H:i:s');
            } else {
                $date = '';
            }

            $newUsers[] = array(
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'lastLogin' => $date,
                'roles' => $user->getRoles()
            );
        }
  
        return $this->render('personal/index.html.twig', [
            'controller_name' => 'PersonalController',
            'users' => $newRegister,
            'users_entry' => $newUsers
        ]);
    }
}
