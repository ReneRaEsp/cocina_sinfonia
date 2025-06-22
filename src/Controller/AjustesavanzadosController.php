<?php

namespace App\Controller;

use App\Entity\Comida;
use App\Entity\Impresoras;
use App\Entity\Mesas;
use App\Entity\Proveedores;
use App\Entity\Stock;
use App\Entity\Info;
use App\Entity\Statscomida;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\TipoComida;
use App\Entity\Zonas;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AjustesavanzadosController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/ajustesavanzados", name="ajustesavanzados")
     */
    public function index(): Response
    {

        $repoTComida = $this->entityManager->getRepository(TipoComida::class);
        $tiposcomidas = $repoTComida->findAll();

        $repoComida = $this->entityManager->getRepository(Comida::class);
        $comidaExtras = $repoComida->findBy(['extra' => true]);

        $repoZonas = $this->entityManager->getRepository(Zonas::class);
        $zonas = $repoZonas->findAll();

        $newsTipo = array();
        foreach ($tiposcomidas as $tcomida) {
            $newsTipo[] = array(
                'id' => $tcomida->getId(),
                'name' => $tcomida->getName(),
                'active' => $tcomida->isActive(),
                'ruta' => $tcomida->getRutaImg(),
            );
        }

        $newsZona = array();
        foreach ($zonas as $zona) {
            $newsZona[] = array(
                'id' => $zona->getId(),
                'name' => $zona->getName(),
                'active' => $zona->isActive(),
            );
        }

        $tiposyComida = array();
        foreach ($tiposcomidas as $tcomida) {
            $comidas = $tcomida->getComida(); // Asumiendo que getComida() devuelve un array de objetos comida
            $nombresComidas = array();

            foreach ($comidas as $comida) {

                if (!$comida->isIsDeleted()) {
                    $arrayExtras = array();
                    foreach ($comida->getPosiblesExtras() as $extra) {
                        $extraName = $repoComida->findOneBy(['id' => $extra]);
                        array_push($arrayExtras, $extraName->getName());
                    }
                    $nombresComidas[] = array(
                        "nombre" => $comida->getName(),
                        "precio" => $comida->getPrecio(),
                        "id" => $comida->getId(),
                        "iscomida" => $comida->isIscomida(),
                        "extras" => $arrayExtras,
                        "numplato" =>  $comida->getNumPlato(),
                        "img" => $comida->getRutaimg() ? 'foodimg/' . $comida->getRutaimg() : '',
                    );
                }
            }

            $tiposyComida[] = array(
                'id' => $tcomida->getId(),
                'name' => $tcomida->getName(),
                'icon' => $tcomida->getRutaImg() ?: ($tcomida->getIcon() ?: null),
                'comidas' => $nombresComidas,
            );
        }


        $Zonas = array();
        foreach ($zonas as $zona) {
            $mesas = $zona->getMesas(); // Asumiendo que getComida() devuelve un array de objetos comida
            $mesasZona = array();

            foreach ($mesas as $mesa) {
                $mesasZona[] = array(
                    "numero" => $mesa->getNumero(),
                    "id" => $mesa->getId(),
                );
            }

            $Zonas[] = array(
                'id' => $zona->getId(),
                'name' => $zona->getName(),
                'mesas' => $mesasZona,
            );
        }



        $iconos = [];

        // Ruta a la carpeta de iconos
        $directorioIconos = $this->getParameter('kernel.project_dir') . '/public/bundles/adminlte/icons';

        // Utiliza el componente Finder para obtener la lista de archivos en el directorio
        $finder = new Finder();
        $finder->files()->in($directorioIconos);

        // Itera sobre los archivos encontrados y agrega los nombres a la lista de iconos
        foreach ($finder as $archivo) {
            $iconos[] = $archivo->getRelativePathname();
        }

        $arrayExtras = array();
        foreach ($comidaExtras as $extra) {
            $arrayExtras[] = array(
                "nombre" => $extra->getName(),
                "precio" => $extra->getPrecio(),
            );
        }

        $repoInfo = $this->entityManager->getRepository(Info::class);
        $info = $repoInfo->findOneBy(['id' => 1]);

        $arrayInfo = array();
        $urlInfo = '';
        if ($info !== null) {
            $urlInfo = $info->getUrl();
            $arrayInfo[] = array(
                "key" => "Nombre",
                "value" => $info->getName(),
            );
            $arrayInfo[] = array(
                "key" => "Dirección",
                "value" => $info->getDir(),
            );
            $arrayInfo[] = array(
                "key" => "Teléfono",
                "value" => $info->getTelf(),
            );
            $arrayInfo[] = array(
                "key" => "Email",
                "value" => $info->getEmail(),
            );
            $arrayInfo[] = array(
                "key" => "CIF",
                "value" => $info->getCif(),
            );
            $arrayInfo[] = array(
                "key" => "Logo",
                "value" => $info->getLogo(),
            );
        } else {

            $urlInfo = 'vacio';
        }

        $impresoras = $this->entityManager->getRepository(Impresoras::class)->findOneBy(['id' => 1]);

        if ($impresoras) {

            $sncocina = $impresoras->getsnCocina();
            $snbarra = $impresoras->getSnBarra();
        } else {
            $sncocina = null;
            $snbarra = null;
        }



        return $this->render('ajustesavanzados/index.html.twig', [
            'tipos_comida' => $newsTipo,
            'iconos' => $iconos,
            'tiposyComidas' => $tiposyComida,
            'zonas' => $newsZona,
            'zonasDisplay' => $Zonas,
            'extras' => $arrayExtras,
            'url' => $urlInfo,
            'info' => $arrayInfo,
            'sncocina' => $sncocina,
            'snbarra' => $snbarra,

        ]);
    }

    /**
     * @Route("/addtipodecomida", name="addTipoComida")
     */
    public function addTipoComida(Request $request)
    {

        $array_tiposComida = json_decode($request->request->get('newTipoComida'), true);
        $arrayIconsString = $request->request->get('arrayIcons');
        $arrayIconos = !empty($arrayIconsString) ? explode(',', $arrayIconsString) : [];
        $arrayImgs = $request->files->get('files');


        $arrayIds = array();
        $arrayNombre = array();
        $arrayImg = array();
        if (isset($array_tiposComida)) {

            foreach ($array_tiposComida as $indice => $tipoComida) {

                $newTipo = new TipoComida();

                $newTipo->setName($tipoComida);
                if ($arrayImgs) {
                    if (!$arrayImgs[$indice]) {
                        return new JsonResponse(['error' => 'No se ha enviado ningún archivo.'], Response::HTTP_BAD_REQUEST);
                    }

                    $foodImgDirectory = $this->getParameter('typefood_image');
                    $filesystem = new Filesystem();

                    // Verificar si la carpeta de subidas existe y crearla si no es así
                    if (!$filesystem->exists($foodImgDirectory)) {
                        try {
                            $filesystem->mkdir($foodImgDirectory, 0755);
                        } catch (IOExceptionInterface $exception) {
                            return new JsonResponse(['error' => 'Error al crear la carpeta de subidas.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                        }
                    }

                    // Obtener el nombre original del archivo
                    $originalFileName = str_replace(' ', '', $tipoComida) . '_';

                    $newImgFood = $originalFileName . '.' . $arrayImgs[$indice]->guessExtension();

                    try {
                        $arrayImgs[$indice]->move($foodImgDirectory, $newImgFood);
                    } catch (FileException $e) {
                        return new JsonResponse(['error' => 'Error al subir el archivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    $newTipo->setRutaImg('typefoodimg/' . $newImgFood);
                } elseif ($arrayIconos) {
                    $newTipo->setIcon('bundles/adminlte/icons/' . $arrayIconos[$indice]);
                }

                $newTipo->setActive(1);

                $this->entityManager->persist($newTipo);
                $this->entityManager->flush();

                $idNuevoTipo = $newTipo->getId();
                $nombreNuevoTipo = $newTipo->getName();
                $rutaImg = $newTipo->getRutaImg();
                array_push($arrayIds, $idNuevoTipo);
                array_push($arrayNombre, $nombreNuevoTipo);
                array_push($arrayImg, $rutaImg);
            }
        }




        return new JsonResponse([
            'added' => 'Se ha añadido correctamente',
            'tipoComidaID' =>  $arrayIds,
            'tipoComida' => $arrayNombre,
            'imgs' => $arrayImg,

        ]);
    }

    /**
     * @Route("/enabletipodecomida", name="enableTipoComida")
     */
    public function enableTipoComida(Request $request)
    {

        // Supongamos que los datos de la solicitud (POST) contienen la información de los checkboxes
        $data = $request->request->get('check');

        // Obtén el repository de la entidad TipoComida
        $repository = $this->entityManager->getRepository(TipoComida::class);

        // Itera sobre los datos y actualiza la base de datos si es necesario
        foreach ($data as $checkboxInfo) {
            $id = $checkboxInfo['id'];
            $checked = filter_var($checkboxInfo['checked'], FILTER_VALIDATE_BOOLEAN);

            // Obtén el TipoComida desde la base de datos
            $tipoComida = $repository->find($id);

            // Verifica si el estado en la base de datos es diferente al estado del checkbox
            if ($tipoComida && $tipoComida->isActive() != $checked) {
                // Actualiza el estado en la base de datos
                $tipoComida->setActive($checked);

                // EntityManager
                $this->entityManager->persist($tipoComida);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse([
            'added' => 'Se ha añadido correctamente'

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

    /**
     * @Route("/modificarnombre", name="modificar_nombre")
     */
    public function modificarNombre(Request $request)
    {
        $newName = $request->request->get('nombreTipo');
        $id = $request->request->get('idTipo');
        $archivo = $request->files->get('archivo'); // Obtiene el archivo

        $directorioImagenes = $this->getParameter('typefood_image');

        $repoTComida = $this->entityManager->getRepository(TipoComida::class);
        $tipoComida = $repoTComida->findOneBy(['id' => $id]);

        // Crea el directorio si no existe
        $filesystem = new Filesystem();
        if (!$filesystem->exists($directorioImagenes)) {
            try {
                $filesystem->mkdir($directorioImagenes, 0755);
            } catch (IOExceptionInterface $exception) {
                return new Response("No se pudo crear el directorio: " . $exception->getMessage(), 500);
            }
        }

        // Si hay un archivo cargado
        if ($archivo) {
            $nombreArchivo = uniqid() . '.' . $archivo->guessExtension(); // Nombre único para evitar colisiones

            try {
                // Mueve el archivo al directorio de destino
                $archivo->move($directorioImagenes, $nombreArchivo);
                $tipoComida->setRutaImg('typefoodimg/' . $nombreArchivo);
            } catch (FileException $e) {
                return new Response("Error al subir la imagen: " . $e->getMessage(), 500);
            }
        }


        $tipoComida->setName(strtoupper($newName));


        $this->entityManager->persist($tipoComida);
        $this->entityManager->flush();






        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'added' => 'Se ha añadido correctamente',
            'ruta' => $tipoComida->getRutaImg() ? $tipoComida->getRutaImg() : null
        ]);
    }

    /**
     * @Route("/eliminartipo", name="eliminar_tipo")
     */
    public function eliminarTipo(Request $request)
    {

        $id = $request->request->get('id');

        $repoTComida = $this->entityManager->getRepository(TipoComida::class);
        $tipoComida = $repoTComida->findOneBy(['id' => $id]);

        $comidas = $this->entityManager->getRepository(Comida::class)->findBy(['type_food' => $tipoComida->getId()]);
        $stocks = $this->entityManager->getRepository(Stock::class)->findBy(['type_food' => $tipoComida->getId()]);

        if ($stocks) {
            foreach ($stocks as $stock) {

                $this->entityManager->remove($stock);
                $this->entityManager->flush();
            }
        }

        if ($comidas) {
            foreach ($comidas as $comida) {

                $this->entityManager->remove($comida);
                $this->entityManager->flush();
            }
        }



        $this->entityManager->remove($tipoComida);
        $this->entityManager->flush();






        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'added' => 'Se ha eliminado correctamente'

        ]);
    }


    /**
     * @Route("/addcomida", name="addcomida")
     */
    public function addComida(Request $request)
    {

        $idTipo = $request->request->get('id');
        $nombre = $request->request->get('nombre');
        $desc = $request->request->get('desc');
        $tipo = $request->request->get('tipo');
        $precio = $request->request->get('precio');
        $unitario = $request->request->get('unitario');
        $cantidad = $request->request->get('cantidad');
        $extras = $request->request->get('extra');
        $stockage = $request->request->get('stockage');
        /** @var UploadedFile $foodImage */
        $foodImage = $request->files->get('img');


        if ($foodImage) {
            $foodImgDirectory = $this->getParameter('food_image');
            $filesystem = new Filesystem();

            // Verificar si la carpeta de subidas existe y crearla si no es así
            if (!$filesystem->exists($foodImgDirectory)) {
                try {
                    $filesystem->mkdir($foodImgDirectory, 0755);
                } catch (IOExceptionInterface $exception) {
                    return new JsonResponse(['error' => 'Error al crear la carpeta de subidas.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            // Obtener el nombre original del archivo
            $originalFileName = $nombre . '_' . $idTipo;

            $newImgFood = $originalFileName . '.' . $foodImage->guessExtension();

            try {
                $foodImage->move($foodImgDirectory, $newImgFood);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Error al subir el archivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }


        $repoTComida = $this->entityManager->getRepository(TipoComida::class);
        $tipoComida = $repoTComida->findOneBy(['id' => $idTipo]);

        $newComida = new Comida();

        $newComida->setName(strtoupper($nombre));
        $newComida->setDescription(strtoupper($desc));
        $newComida->setTypeFood($tipoComida);
        $newComida->setPrecio(floatval($precio));

        if ($foodImage) {
            $newComida->setRutaimg($newImgFood);
        }




        if (isset($extras) &&  !empty($extras)) {
            $arrayExtras = array();
            foreach ($extras as $extra) {
                $repoComida = $this->entityManager->getRepository(Comida::class);
                $comidaExtra = $repoComida->findOneBy(['name' => $extra]);
                array_push($arrayExtras, $comidaExtra->getId());
            }
            $newComida->setPosiblesextras($arrayExtras);
        }




        if ($tipo === 'comida') {
            $newComida->setIscomida(true);
        } else {
            $newComida->setIsbebida(true);
        }

        $newComida->setUnitario(true);

        $newComida->setIsDeleted(false);





        $this->entityManager->persist($newComida);
        $this->entityManager->flush();

        if ($stockage === 'true') {
            $newStock = new Stock();

            $newStock->setName(strtoupper($nombre));
            $newStock->setDescription('');
            $newStock->setAmount($cantidad);
            // $newStock->setAmount(0);


            $repoComida = $this->entityManager->getRepository(Comida::class);
            $idComida = $repoComida->findOneBy(['id' => $newComida->getId()]);

            $newStock->setComida($idComida);
            $newStock->setTypeFood($tipoComida);


            $this->entityManager->persist($newStock);
            $this->entityManager->flush();
        }





        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'added' => 'Se ha añadido correctamente',
            'id' => $newComida->getId(),
            'nombre' => $newComida->getName(),
            'precio' => number_format($newComida->getPrecio(), 2),
            'iscomida' => $newComida->isIscomida(),
            'extras' => $extras,
            'img' => $newComida->getRutaimg(),

        ]);
    }
    /**
     * @Route("/addzona", name="addzona")
     */
    public function addZona(Request $request)
    {
        $array_zona = $request->request->get('newZona');

        $arrayIds = array();
        $arrayNombre = array();
        if (isset($array_zona)) {

            foreach ($array_zona as $indice => $zona) {

                $newZona = new Zonas();

                $newZona->setName($zona);
                $newZona->setActive(1);

                $this->entityManager->persist($newZona);
                $this->entityManager->flush();

                $idNuevaZona = $newZona->getId();
                $nombreNuevaZona = $newZona->getName();
                array_push($arrayIds, $idNuevaZona);
                array_push($arrayNombre, $nombreNuevaZona);
            }
        }



        return new JsonResponse([
            'added' => 'Se ha añadido correctamente',
            'tipoComidaID' =>  $arrayIds,
            'tipoComida' => $arrayNombre,

        ]);
    }

    /**
     * @Route("/enablezona", name="enablezona")
     */
    public function enableZona(Request $request)
    {

        // Supongamos que los datos de la solicitud (POST) contienen la información de los checkboxes
        $data = $request->request->get('check');

        // Obtén el repository de la entidad TipoComida
        $repository = $this->entityManager->getRepository(Zonas::class);

        // Itera sobre los datos y actualiza la base de datos si es necesario
        foreach ($data as $checkboxInfo) {
            $id = $checkboxInfo['id'];
            $checked = filter_var($checkboxInfo['checked'], FILTER_VALIDATE_BOOLEAN);

            // Obtén el TipoComida desde la base de datos
            $zona = $repository->find($id);

            // Verifica si el estado en la base de datos es diferente al estado del checkbox
            if ($zona && $zona->isActive() != $checked) {
                // Actualiza el estado en la base de datos
                $zona->setActive($checked);

                // EntityManager
                $this->entityManager->persist($zona);
                $this->entityManager->flush();
            }
        }

        return new JsonResponse([
            'added' => 'Se ha añadido correctamente'

        ]);
    }

    /**
     * @Route("/modificarzona", name="modificar_zona")
     */
    public function modificarZona(Request $request)
    {
        $newName = $request->request->get('nombreZona');
        $id = $request->request->get('idZona');

        $repoZonas = $this->entityManager->getRepository(Zonas::class);
        $zona = $repoZonas->findOneBy(['id' => $id]);

        $zona->setName(strtoupper($newName));

        $this->entityManager->persist($zona);
        $this->entityManager->flush();






        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'added' => 'Se ha añadido correctamente',
        ]);
    }

    /**
     * @Route("/eliminarzona", name="eliminar_zona")
     */
    public function eliminarZona(Request $request)
    {

        $id = $request->request->get('id');

        $repoZonas = $this->entityManager->getRepository(Zonas::class);
        $zona = $repoZonas->findOneBy(['id' => $id]);

        $this->entityManager->remove($zona);
        $this->entityManager->flush();






        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'added' => 'Se ha añadido correctamente'

        ]);
    }

    /**
     * @Route("/addmesa", name="addmesa")
     */
    public function addMesa(Request $request)
    {

        $idZona = $request->request->get('id');
        $numero = $request->request->get('numero');
        $icono = $request->request->get('imageUrl');




        $repoZonas = $this->entityManager->getRepository(Zonas::class);
        $zona = $repoZonas->findOneBy(['id' => $idZona]);

        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $ultimoId = $repoMesas->getLastId();
        $nuevoIdMesa = $ultimoId + 1;

        $mesa = new Mesas();

        $mesa->setNumero($numero);
        $mesa->setPagado(0);
        $mesa->setPorPagar(0);
        if ($idZona === '1') {
            $mesa->setLocalizacion('C');
        } else if ($idZona === '2') {
            $mesa->setLocalizacion('T');
        } else {
            $mesa->setLocalizacion('Barra');
        }
        $mesa->setZonas($zona);
        $mesa->setComensales(0);
        $mesa->setIcon($icono);

        $this->entityManager->persist($mesa);
        $this->entityManager->flush();



        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'added' => 'Se ha añadido correctamente'

        ]);
    }

    /**
     * @Route("/datoscomida", name="datos_comida")
     */
    public function datosComida(Request $request)
    {

        $pathImg = null;
        $id = $request->request->get('id');
        $nombre = $request->request->get('nombre');
        $precioString = $request->request->get('precio');
        $precioString = str_replace(',', '.', $precioString); // Reemplazar la coma por un punto
        $precio = floatval($precioString);
        $suplementos = $request->request->get('suplementos');
        $ordenplato = $request->request->get('ordenplato');
        $typeFood = trim($request->request->get('typeFood'));
        /** @var UploadedFile $foodImage */
        $foodImage = $request->files->get('img');

        if ($foodImage) {
            $foodImgDirectory = $this->getParameter('food_image');
            $filesystem = new Filesystem();

            // Verificar si la carpeta de subidas existe y crearla si no es así
            if (!$filesystem->exists($foodImgDirectory)) {
                try {
                    $filesystem->mkdir($foodImgDirectory, 0755);
                } catch (IOExceptionInterface $exception) {
                    return new JsonResponse(['error' => 'Error al crear la carpeta de subidas.'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            }

            $randNumber = rand(0, 122);

            // Generar una cadena aleatoria de 5 caracteres
            $randString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);

            // Obtener el nombre original del archivo
            $originalFileName = $randNumber . $randString;

            $newImgFood = $originalFileName . '.' . $foodImage->guessExtension();

            try {
                $foodImage->move($foodImgDirectory, $newImgFood);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Error al subir el archivo.'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }




        $repoComida = $this->entityManager->getRepository(Comida::class);
        $comida = $repoComida->findOneBy(['id' => $id]);

        if ($suplementos !== '') {
            $arraySup = array();
            foreach ($suplementos as $sup) {
                $comidaSup = $repoComida->findOneBy(['name' => $sup]);

                array_push($arraySup, $comidaSup->getId());
            }
            $comida->setPosiblesextras($arraySup);
        }

        $comida->setName($nombre);
        $comida->setPrecio($precio);
        $comida->setNumPlato(intval($ordenplato));
        if(isset($newImgFood)){
            $comida->setRutaimg($newImgFood);
            $pathImg = 'foodimg/' . $comida->getRutaimg();
        }

        if($typeFood == "comida"){
            $comida->setIscomida(1);
            $comida->setIsbebida(null);

        } elseif($typeFood == "bebida"){
            $comida->setIsbebida(1);
            $comida->setIscomida(null);

        } 
        



        $this->entityManager->persist($comida);
        $this->entityManager->flush();



        


        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([
            'nombre' => $nombre,
            'precio' => number_format($precio, 2),
            'img' => $pathImg !== null ? $pathImg : '',

        ]);
    }
    /**
     * @Route("/eliminarcomidaajustes", name="eliminar_comida_ajustes")
     */
    public function eliminarComida(Request $request)
    {

        $id = $request->request->get('id');

        $repoComida = $this->entityManager->getRepository(Comida::class);
        $comidaBorrar = $repoComida->findOneBy(['id' => $id]);

        $stockBorrar = $this->entityManager->getRepository(Stock::class)->findOneBy(['comida' => $id]);


        $comidaBorrar->setIsDeleted(1);
        if ($stockBorrar) {
            $this->entityManager->remove($stockBorrar);
        }

        $this->entityManager->flush();





        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([], 200);
    }

    /**
     * @Route("/eliminarmesaajustes", name="eliminar_mesa_ajustes")
     */
    public function eliminarMesa(Request $request)
    {

        $id = $request->request->get('id');

        $repoMesas = $this->entityManager->getRepository(Mesas::class);
        $mesaBorrar = $repoMesas->find($id);

        if (!$mesaBorrar) {
            return new JsonResponse(['message' => 'La mesa no fue encontrada'], 404);
        }

        // Obtener todas las ventas asociadas a esta mesa
        $ventas = $mesaBorrar->getVentas();

        // Desvincular cada venta de la mesa
        foreach ($ventas as $venta) {
            $venta->setMesa(null); // Establece la mesa asociada a null
            $this->entityManager->persist($venta);
            $this->entityManager->flush();
        }

        // Eliminar la mesa

        $this->entityManager->remove($mesaBorrar);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Mesa eliminada exitosamente'], 200);
    }
    /**
     * @Route("/addinfo", name="addinfo")
     */
    public function actualizarDatos(Request $request)
    {

        $name = $request->request->get('name');
        $dir = $request->request->get('dir');
        $telf = $request->request->get('telf');
        $email = $request->request->get('email');
        $cif = $request->request->get('cif');
        $logo = $request->files->get('logo');

        if ($logo) {
            $filesystem = new Filesystem();

            // Obtener el directorio "public" de tu proyecto Symfony
            $publicDirectory = $this->getParameter('kernel.project_dir') . '/public';

            // Crear la carpeta "img_logo" si no existe
            $logoDirectory = $publicDirectory . '/img_logo';
            try {
                $filesystem->mkdir($logoDirectory);
            } catch (IOExceptionInterface $exception) {
                // Manejar errores al crear la carpeta
                throw new \RuntimeException("Error al crear la carpeta: " . $exception->getMessage());
            }

            // Eliminar todas las fotos existentes en la carpeta "img_logo"
            $files = glob($logoDirectory . '/*'); // Obtener todos los archivos en el directorio
            foreach ($files as $file) { // Iterar sobre los archivos
                if (is_file($file)) { // Verificar si es un archivo
                    unlink($file); // Eliminar el archivo
                }
            }

            // Generar un nombre único para la imagen (por ejemplo, usando un UUID)
            $logoFileName = 'logo_' . '.' . $logo->guessExtension();

            // Mover el archivo al directorio "img_logo" con el nombre generado
            $logo->move($logoDirectory, $logoFileName);
        }

        $repoInfo = $this->entityManager->getRepository(Info::class);
        $info = $repoInfo->findOneBy(['id' => 1]);

        if ($info) {

            $info->setName($name);
            $info->setDir($dir);
            $info->settelf($telf);
            $info->setEmail($email);
            $info->setCif($cif);
            if ($logo) {
                $info->setLogo('img_logo/' . $logoFileName);
            }


            $this->entityManager->persist($info);
            $this->entityManager->flush();
        } else {
            $baseUrl = $request->getSchemeAndHttpHost(); // Esto te dará la URL base, como "http://localhost:8000" en entorno de desarrollo

            $newInfo = new Info();

            $newInfo->setName($name);
            $newInfo->setDir($dir);
            $newInfo->settelf($telf);
            $newInfo->setEmail($email);
            $newInfo->setCif($cif);
            if ($logo) {
                $newInfo->setLogo('img_logo/' . $logoFileName);
            }

            $newInfo->setUrl($baseUrl);


            $this->entityManager->persist($newInfo);
            $this->entityManager->flush();
        }

        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([], 200);
    }

    /**
     * @Route("/actualizarimpresoras", name="actualizar_impresoras")
     */
    public function actualizarImpresoras(Request $request)
    {

        $sncocina = $request->request->get('sncocina');
        $snbarra = $request->request->get('snbarra');

        $impresoras = $this->entityManager->getRepository(Impresoras::class)->findOneBy(['id' => 1]);

        if ($impresoras) {

            $impresoras->setsncocina($sncocina);
            $impresoras->setsnbarra($snbarra);

            $this->entityManager->flush();
        } else {
            $impresoras = new Impresoras;
            $impresoras->setsncocina($sncocina);
            $impresoras->setsnbarra($snbarra);

            $this->entityManager->persist($impresoras);
            $this->entityManager->flush();
        }





        // Renderiza una plantilla Twig para mostrar la lista de iconos
        return new JsonResponse([], 200);
    }
}
