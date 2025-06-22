<?php
// src/EventSubscriber/MenuBuilderSubscriber.php
namespace App\EventSubscriber;

use KevinPapst\AdminLTEBundle\Event\SidebarMenuEvent;
use KevinPapst\AdminLTEBundle\Model\MenuItemModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class MenuBuilderSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    public static function getSubscribedEvents(): array
    {
        return [
            SidebarMenuEvent::class => ['onSetupMenu', 100],
        ];
    }
    
    /**
     * Generate the main menu.
     *
     * @param SidebarMenuEvent $event
     */
    public function onSetupMenu(SidebarMenuEvent $event)
    {
        $logoutUrl = $this->urlGenerator->generate('fos_user_security_logout');

        $items = [];
        
        $dashboard = new MenuItemModel('dashboardId', 'Dashboard', 'dashboard', [], 'fas fa-tachometer-alt');
        $stock = new MenuItemModel('stockId', 'Stock', 'stock', [], 'fas fa-boxes');
        $proveedores = new MenuItemModel('proveedoresId', 'Proveedores', 'proveedores', [], 'fas fa-handshake');
        $personal = new MenuItemModel('personalId', 'Personal', 'personal', [], 'fas fa-users');
        $personal->addChild(new MenuItemModel('fihajeId', 'Fichaje', 'fichaje', [], 'fa-solid fa-clock'))
        ->addChild(new MenuItemModel('personalId', 'Registro', 'personal', [], 'fa-solid fa-book'));
        $estadisticas = new MenuItemModel('estadisticasId', 'Estadísticas', 'estadisticas', [], 'fas fa-chart-line');
        $estadisticas->addChild(new MenuItemModel('comidaId', 'Estadística Comida', 'statscomida', [], 'fa-solid fa-chart-pie'))
        ->addChild(new MenuItemModel('bebidaId', 'Estadística Bebida', 'statsbebida', [], 'fa-solid fa-chart-pie'))
        ->addChild(new MenuItemModel('comensalesId', 'Estadística Comensales', 'statscomensales', [], 'fa-solid fa-chart-pie'));
        $ventas = new MenuItemModel('ventasId', 'Ventas', 'ventas', [], 'fas fa-euro-sign');
	    $cocina = new MenuItemModel('cocinaId', 'Cocina', 'app_cocina', [], 'fas fa-fire-burner');
        $mesas = new MenuItemModel('mesasId', 'Mesas', 'mesas', [], 'fas fa-utensils');
        $caja = new MenuItemModel('cajaId', 'Caja', 'caja', [], 'fa-solid fa-cash-register');
        $tienda = new MenuItemModel('tiendaId', 'Tienda', 'productostienda', [], 'fa-solid fa-cart-shopping');
        $reservas = new MenuItemModel('reservasId', 'Reservas', 'reservas', [], 'fa-solid fa-calendar-days');
        $facturas = new MenuItemModel('facturasId', 'Facturas', 'facturas', [], 'fa-solid fa-file-invoice-dollar');
        $facturas->addChild(new MenuItemModel('facturasId', 'Facturas Recibidas', 'facturas', [], 'fa-solid fa-file-import'))
        ->addChild(new MenuItemModel('tickettofacturaId', 'Facturas Emitidas', 'tickettofactura', [], 'fa-solid fa-file-export'));
        $ajustes = new MenuItemModel('ajustesId', 'Ajustes', 'app_ajustes', [], 'fa-solid fa-gears');
        $ajustes->addChild(new MenuItemModel('ajustesId', 'Gestión Usuarios', 'app_ajustes', [], 'fa-solid fa-user-group'))
        ->addChild(new MenuItemModel('ajustesAvanzadosId', 'Ajustes Avanzados', 'ajustesavanzados', [], 'fa-solid fa-sliders'));
        $logout = new MenuItemModel('logoutId', 'Logout', $logoutUrl, [], 'fas fa-sign-out-alt');

        $items [] = $mesas;
        $items [] = $caja;
        // $items [] = $dashboard;
        $items [] = $stock;
        $items [] = $proveedores;
        $items [] = $personal;
        $items [] = $cocina;
        $items [] = $estadisticas;
        $items [] = $ventas;
        $items [] = $reservas;
        $items [] = $facturas;
        // $items [] = $tienda;
        $items [] = $ajustes;
        $items [] = $logout;

        foreach ($items as $item) {
            $event->addItem($item);
        }
        

        $this->activateByRoute(
            $event->getRequest()->get('_route'),
            $event->getItems()
        );
    }

    /**
     * @param string $route
     * @param MenuItemModel[] $items
     */
    protected function activateByRoute($route, $items)
    {
        foreach ($items as $item) {
            if ($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            } elseif ($item->getRoute() == $route) {
                $item->setIsActive(true);
            }
        }
    }
}
