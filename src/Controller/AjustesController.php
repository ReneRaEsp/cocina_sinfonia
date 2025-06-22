<?php

namespace App\Controller;

use App\Entity\Personalizacion;
use App\Entity\Auditoria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\RegistrationController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Model\UserManagerInterface;
use DateTime;


class AjustesController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/ajustes", name="app_ajustes")
     */
    public function index(Request $request, RegistrationController $registrationController,  SessionInterface $session, UserManagerInterface $userManager): Response
    {
        $response = $registrationController->registerAction($request, $session);
        $content = $response->getContent();

        $repoPers = $this->entityManager->getRepository(Personalizacion::class);
        $personalizacion = $repoPers->findAll();

        $repoAudit = $this->entityManager->getRepository(Auditoria::class);
        $auditorias = $repoAudit->findAll();

        $users = $userManager->findUsers();


        $newUsers = array();
        foreach ($users as $user) {
            $lastLogin = $user->getLastLogin();
            $date = $lastLogin ? $lastLogin->format('d-m-Y') : '';

            $newUsers[] = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'lastLogin' => $date,
                'roles' => $user->getRoles()
            );
        }


        $newPers = array();
        foreach ($personalizacion as $pers) {
            $newPers[] = array(
                'id' => $pers->getId(),
                'name' => $pers->getName(),
                'path' => $pers->getPath(),
                'active' => $pers->isActive()
            );
        }

        $newAudit = array();
        foreach ($auditorias as $audit) {
            $newAudit[] = array(
                'usuario' => $audit->getUsuario(),
                'modificado' => $audit->getModificado(),
                'fecha' => $audit->getFecha()->format('d-m-Y'),
                'rol_anterior' => $audit->getRolAnterior(),
                'rol_nuevo' => $audit->getRolNuevo(),
            );
        }

        return $this->render('ajustes/index.html.twig', [
            'content' => $content,
            'users' => $newUsers,
            'personalizaciones' => $newPers,
            'mensajeFlash' => '',
            'auditorias' => $newAudit,
        ]);
    }


    /**
     * @Route("/updatetheme", name="updatetheme")
     */
    public function updateTheme(Request $request)
    {

        $id = $request->request->get('check');
        $path = '';

        $repoPers = $this->entityManager->getRepository(Personalizacion::class);
        $personalizacion = $repoPers->findAll();

        foreach ($personalizacion as $pers) {
            if ($pers->getId() === intval($id)) {
                //Cambiar el tema de la pagina
                $pers->setActive(1);
                $path = $pers->getPath();
            } else {
                $pers->setActive(0);
            }
            $this->entityManager->persist($pers);
            $this->entityManager->flush();
        }

        $repoPers = $this->entityManager->getRepository(Personalizacion::class);
        $personalizacion = $repoPers->findOneBy(['active' => true]);
        $request->getSession()->set('tabulator_route', $personalizacion->getPath());

        $repoAudit = $this->entityManager->getRepository(Auditoria::class);
        $auditorias = $repoAudit->findAll();

        $newAudit = array();
        foreach ($auditorias as $audit) {
            $newAudit[] = array(
                'usuario' => $audit->getUsuario(),
                'modificado' => $audit->getModificado(),
                'fecha' => $audit->getFecha()->format('d-m-Y'),
                'rol_anterior' => $audit->getRolAnterior(),
                'rol_nuevo' => $audit->getRolNuevo(),
            );
        }


        return new JsonResponse([
            'ruta_tabulator' => $path,
            'changed' => "Se ha actualizado correctamente el tema de tus tablas",
            'auditorias' => $newAudit

        ]);
    }

    /**
     * @Route("/setrole", name="set_role", methods={"POST"})
     */
    public function setRole(Request $request,  UserManagerInterface $userManager)
    {
        $data = json_decode($request->getContent(), true);
        $role = $data['value'];
	$email = $data['email'];
     
	$auditoria = new Auditoria();

        // Buscar el usuario por el correo electrónico
        $user = $userManager->findUserByEmail($email);

        $auditoria->setUsuario($user->getUsername());
        $auditoria->setModificado($this->getUser()->getUsername());
        $auditoria->setFecha(new DateTime());
        $auditoria->setRolAnterior(implode(', ', $user->getRoles()));
        $auditoria->setRolNuevo(implode(', ', $role));

        if (!$user) {
            return new JsonResponse(['error' => 'Usuario no encontrado.']);
        }

        // Actualizar el rol del usuario y guardar los cambios
        $user->setRoles($role);
        $userManager->updateUser($user);

        $this->entityManager->persist($auditoria);
        $this->entityManager->flush();

        return new JsonResponse([
            'texto' => 'Actualizado con éxito',
        ], 200);
    }

    /**
     * @Route("/deleteuser", name="delete_user", methods={"DELETE"})
     */
    public function deleteUser(Request $request,  UserManagerInterface $userManager)
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'];

        $user = $userManager->findUserBy(['id' => $id]);

        if (!$user) {
            throw $this->createNotFoundException('Usuario no encontrado.');
        }

        $userManager->deleteUser($user);

        $users = $userManager->findUsers();


        $newUsers = array();
        foreach ($users as $user) {
            $lastLogin = $user->getLastLogin();
            $date = $lastLogin ? $lastLogin->format('d-m-Y') : '';

            $newUsers[] = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'lastLogin' => $date,
                'roles' => $user->getRoles()
            );
        }

        return new JsonResponse([
            'user' => 'El usuario se ha eliminado correctamente',
            'users' => $newUsers
        ], 200);
    }
}
