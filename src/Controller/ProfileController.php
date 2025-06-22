<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ProfileController extends AbstractController
{

    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
   /**
     * @Route("/profile", name="profile_show")
     */
    public function show(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserProfileType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setEmail($form->get('email')->getData());
            $user->setUsername($form->get('username')->getData());

            // Handle password encoding
            if ($form->get('plainPassword')->getData()) {
                $encodedPassword = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());
                $user->setPassword($encodedPassword);
            }

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->addFlash('success', 'El usuario se ha actualizado correctamente!');

            // return $this->redirectToRoute('profile_show');
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
