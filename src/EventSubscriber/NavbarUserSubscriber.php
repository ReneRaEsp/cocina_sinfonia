<?php
// src/EventSubscriber/NavbarUserSubscriber.php
namespace App\EventSubscriber;


use KevinPapst\AdminLTEBundle\Event\ShowUserEvent;
use KevinPapst\AdminLTEBundle\Event\NavbarUserEvent;
use KevinPapst\AdminLTEBundle\Event\SidebarUserEvent;
use KevinPapst\AdminLTEBundle\Model\UserModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use KevinPapst\AdminLTEBundle\Model\DropdownMenuItemModel;
use KevinPapst\AdminLTEBundle\Model\NavBarUserLink;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class NavbarUserSubscriber implements EventSubscriberInterface
{
    private $security;
    private $urlGenerator;
    

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, FlashBagInterface $flashBag)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NavbarUserEvent::class => ['onShowUser', 100],
            SidebarUserEvent::class => ['onShowUser', 100],
        ];
    }

    public function onShowUser(ShowUserEvent $event)
    {
        if (null === $this->security->getUser()) {
            return;
        }
        

        /* @var $myUser User */
        $myUser = $this->security->getUser();

        $user = new UserModel();
        $user
            ->setId($myUser->getId())
            ->setName($myUser->getUsername())
            ->setUsername($myUser->getUsername())
            ->setIsOnline(true)
            ->setTitle('demo user')
            // ->setAvatar($myUser->getAvatar())
            // ->setMemberSince($myUser->getRegisteredAt())
            
        ;
        $logoutUrl = $this->urlGenerator->generate('fos_user_security_logout');
        $logoutItem = new MenuItemModel('logout', 'Logout', $logoutUrl, [], 'fas fa-sign-out-alt');
        // $user->setDropdownItems([$logoutItem]);

        $logout_link = new NavBarUserLink('Logout', 'fos_user_security_logout', []);
        $event->setUser($user);
        $event->addLink($logout_link);
        $event->setShowLogoutLink(true);
       
    }
}