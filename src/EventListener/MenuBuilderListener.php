<?php
// src/EventListener/MenuBuilderListener.php
namespace App\EventListener;

use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\HttpFoundation\Request;

class MenuBuilderListener
{
    public function onSetupMenu(SidebarMenuEvent $event)
    {
        $request = $event->getRequest();
    
        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }
    }
    
    protected function getMenu(Request $request)
    {
        $blog = new MenuItemModel('ItemId', 'ItemDisplayName', 'item_symfony_route', [], 'iconclasses fa fa-plane');
    
        $blog->addChild(
            new MenuItemModel('ChildOneItemId', 'ChildOneDisplayName', 'child_1_route', [], 'fa fa-rss-square')
        )->addChild(
            new MenuItemModel('ChildTwoItemId', 'ChildTwoDisplayName', 'child_2_route')
        );
        
        return $this->activateByRoute($request->get('_route'), [$blog]);
    }
    
    /**
     * @param string $route
     * @param MenuItemModel[] $items
     * @return MenuItemModel[]
     */
    protected function activateByRoute($route, $items)
    {
        foreach($items as $item) {
            if($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            } elseif($item->getRoute() == $route) {
                $item->setIsActive(true);
            }
        }
    
        return $items;
    }
}