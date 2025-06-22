<?php 

// src/EventListener/LoginListener.php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RegistroUsuarios; // Reemplaza "TuEntidadDeRegistro" con el nombre de tu entidad de registro

class LoginListener implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {        
        $user = $event->getAuthenticationToken()->getUser();

        $registro = new RegistroUsuarios();
        $registro->setName($user->getUserIdentifier());
        $registro->setEmail($user->getEmailCanonical());
        $registro->setFecha(new \DateTime());

        $this->entityManager->persist($registro);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            'security.interactive_login' => 'onSecurityInteractiveLogin',
        ];
    }
}
