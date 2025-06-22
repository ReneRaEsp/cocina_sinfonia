<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;

class IconController extends AbstractController
{
    /**
     * @Route("/icon", name="app_icon")
     */
    public function index(): Response
    {
        $iconos = [];

        // Ruta a la carpeta de iconos
        $directorioIconos = $this->getParameter('kernel.project_dir') . '/public/icons';

        // Utiliza el componente Finder para obtener la lista de archivos en el directorio
        $finder = new Finder();
        $finder->files()->in($directorioIconos);

        // Itera sobre los archivos encontrados y agrega los nombres a la lista de iconos
        foreach ($finder as $archivo) {
            $iconos[] = $archivo->getRelativePathname();
        }
        return $this->render('icon/index.html.twig', [
            'controller_name' => 'IconController',
            'iconos' => $iconos,
        ]);
    }

    /**
     * @Route("/lista-iconos", name="lista_iconos")
     */
    public function listaIconos()
    {
        $iconos = [];

        // Ruta a la carpeta de iconos
        $directorioIconos = $this->getParameter('kernel.project_dir') . '/public/icons';

        // Utiliza el componente Finder para obtener la lista de archivos en el directorio
        $finder = new Finder();
        $finder->files()->in($directorioIconos);

        // Itera sobre los archivos encontrados y agrega los nombres a la lista de iconos
        foreach ($finder as $archivo) {
            $iconos[] = $archivo->getRelativePathname();
        }

        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return $this->render('icon/index.html.twig', [
            'iconos' => $iconos,
        ]);
    }
}
