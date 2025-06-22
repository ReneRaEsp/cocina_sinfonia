<?php
// src/EventListener/NavbarUserListener.php
namespace App\EventListener;

use KevinPapst\AdminLTEBundle\Event\ShowUserEvent;
use KevinPapst\AdminLTEBundle\Model\NavBarUserLink;
use KevinPapst\AdminLTEBundle\Model\UserModel;

class NavbarUserListener
{
    public function onShowUser(ShowUserEvent $event)
    {
        $user = $this->getUser();
        $event->setUser($user);
        
        $event->setShowProfileLink(false);
    
        $event->addLink(new NavBarUserLink('Followers', 'logout'));
        $event->addLink(new NavBarUserLink('Sales', 'logout'));
        $event->addLink(new NavBarUserLink('Friends', 'logout', ['id' => 2]));
    }
    
    protected function getUser()
    {
        // retrieve your concrete user model or entity
        // see above in NavbarUserSubscriber for a full example
        return new User();
    }

}