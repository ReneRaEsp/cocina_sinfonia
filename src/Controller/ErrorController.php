<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/error", name="error")
     */
    public function showError()
    {
        
        return $this->render('errors/erroraccess.html.twig');
    }

    /**
     * @Route("/error/403", name="error403")
     */
    public function accessDenied()
    {
        return $this->render('errors/access_denied.html.twig');
    }
}
