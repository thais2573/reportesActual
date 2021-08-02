<?php

namespace AppBundle\Controller;

use AppBundle\Entity\util;
use AppBundle\Entity\utilDetalles;
use Composer\Json\JsonFile;
use DH\DoctrineAuditBundle\Tests\Fixtures\Core\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class utilesController extends Controller
{
    /**
     * @Route("reportes/lista_utiles", name="lista_utiles")
     */
    public function listaUtilesAction(Request $request, $maxItemPerPage = 10)
    {
        $ordenar = null;
        $direccion = null;
        // dump($request);
        if ($request->query->get('maxItemPerPage')) {
            $maxItemPerPage = $request->query->get('maxItemPerPage');
        }
        if ($request->query->get('sort')) {
            //  $maxItemPerPage = $request->query->get('maxItemPerPage');
            $ordenar = $request->query->get('sort');
            $direccion = $request->query->get('direction');
//            dump($request);
//            die();
        }
        //   dump($maxItemPerPage);
        // die();
        $entityManager = $this->getDoctrine()->getManager();
        // $select = $request->request->get('componente');
        // dump($select);
        // die();
        $accesorio = '';
        //  $accesorio = $this->getDoctrine()->getRepository('AppBundle:' . $select)->findAll();

///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }

            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }
        $usuario_ums = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findDetallesUmsxusuario($this->getUser()->getUsername());
        $dep = $usuario_ums[0]['dep'];

        switch ($dep) {
            case 'Informatica y Comunicaciones':
                $dep = 'Computacion';
                break;
            case 'Registros Medicos':
                $dep = 'REGISTROS MEDICOS';
                break;
            case 'Asesor Juridico':
                $dep = 'JURIDICO';
                break;
            case 'Cuadros':
                $dep = 'CUADROS';
                break;
            case 'Organos Asesores':
                $dep = 'CONSULTA COOPERANTES ';
                break;
//                case 'Segmento Anterior':
//                $dep=' ';
//                break;
            case 'Consulta Externa':
                $dep = 'CONSULTA EXTERNA ';
                break;
            case 'Estomatologia':
                $dep = 'ESTOMATOLOGIA';
                break;
            case 'Medicina Interna':
                $dep = 'MEDICINA INTERNA';
                break;
//                case 'Anestesia y Reanimacion':
//                $dep='';
//                break;
            case 'Docencia e Investigaciones':
                $dep = 'DOCENCIA MEDICA      ';
                break;
            case 'Farmacia':
                $dep = 'FARMACIA';
                break;
            case 'Seguridad y Proteccion':
                $dep = 'PROTECCION FISICA';
                break;
            case 'Rayos x y Laboratorio':
                $dep = 'IMAGENEOLOGIA';
                break;
            case 'Epidemiologia':
                $dep = '';
                break;
            case 'Dietetico y Cocina':
                $dep = 'DIETETICO COCINA ';
                break;
            case 'Servicios Generales':
                $dep = 'SERVICIOS GENERALES ';
                break;
            case 'Abastecimiento':
                $dep = 'ABASTECIMIENTO';
                break;
            case 'Transporte':
                $dep = 'TRANSPORTE';
                break;
            case 'Mantenimiento':
                $dep = 'MANTENIMIENTO';
                break;
            case 'Electromedicina':
                $dep = 'ELECTROMEDICINA';
                break;
            case 'Grupo Comercial Interno':
                $dep = 'VICE DIRECCION COMERCIAL';
                break;
            case 'Contabilidad':
                $dep = 'CONTABILIDAD';
                break;
            case 'Facturacion':
                $dep = 'FACTURACION';
                break;
            case 'Recursos Humanos':
                $dep = 'RECURSOS HUMANOS';
                break;
            case 'Enfermeria':
                $dep = 'JEFATURA ENFERMERIA   ';
                break;
            case 'Vicedireccion Programa Nacional de Retinosis Pigmentaria':
                $dep = 'VICDIR PROGRAMA NAC.';
                break;
            case 'Vicedireccion Primera':
                $dep = 'V.DIREC. PRIMERA ';
                break;
            case 'Direccion':
                $dep = 'DIRECCION GRAL';
                break;
            case 'Vicedireccion Administrativa':
                $dep = 'VICE DIRECCION ADMINISTRATIVA ';
                break;
        }

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:area');
        $queryArea = $repository->createQueryBuilder('tabla')
            ->andWhere('tabla.nombre =:area')
            ->setParameter('area', $dep)
//            ->andWhere('tabla.user=:usuario_actual')
//            ->setParameter('usuario_actual', $this->getUser()->getUsername())
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
        $lista_aImprimir = [];
        $cont = 0;
        //  dump($this->getUser()->getRol() );die();
        if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_TECNICO') or $this->isGranted('ROLE_AFT')) {
//            $applicationRepository = $entityManager->getRepository('AppBundle:equipoAssets');
//            $activos = $applicationRepository->findBy(['activo' => 1]);
//            $activos = $this->getDoctrine()
//                ->getRepository('AppBundle:utilDetalles')->findAll();
//            $repository = $this->getDoctrine()
//                ->getRepository('AppBundle:utilDetalles');
//            $qb = $entityManager->createQueryBuilder();
//            $qb->select(array('t','t.util'))
//              ->from('AppBundle\Entity\utilDetalles', 't')
//                ->leftJoin('t.util', 'u', \Doctrine\ORM\Query\Expr\Join::WITH, 't.util = u')
////                ->where("i.tipo = 'Movimiento Activo Fijo' ")
//                ->orderBy('t.id', 'DESC');
//            $activos = $qb->getQuery();
            //   $activos = $query->getResult();
            // dump($results);die();
//            $activos = $repository->createQueryBuilder('t')
//                ->innerJoin('AppBundle:util', 'u')
//                ->setParameter('', $value)
//                ->andWhere('u.activo=:estado')
//                ->setParameter('estado', 1)
//                ->orderBy('t.id', 'desc')
//                ->getQuery();
//            dump($activos);
//            die();
//            $dql = "SELECT t FROM AppBundle:equipoAssets t where t.activo=1 ";
//           // $lista = $entityManager->createQuery($dql);
//            $idcosto = $activos->execute()[0]->getIdArea();
//            //$idcosto = $query->execute()[0]->getIdArea();
            // $lista = $entityManager->getRepository('AppBundle:equipoAssets','t')->findAll();
//
            $dql = "SELECT t.id,u.idUH,u.descripcion,t.cantidad,t.nombreCosto,t.nombreArea,t.codigoCosto,t.codigoArea FROM 
               AppBundle:utilDetalles t INNER JOIN AppBundle:util u WHERE t.util = u.id  ORDER BY t.id ";
            $query = $entityManager->createQuery($dql);
//            foreach ($query->execute() as $a){
//                $lista_aImprimir[$cont]['codigo']=$a[0]->getUtil()->getIdUH();
//                $lista_aImprimir[$cont]['descripcion']=$a->getUtil()->getDescripcion();
//                $lista_aImprimir[$cont]['area']=$a->getNombreArea().$a->getCodigoArea();
//                $lista_aImprimir[$cont]['costo']=$a->getNombreCosto().$a->getCodigoCosto();
//                $lista_aImprimir[$cont]['cantidad']=$a->getCantidad();
//                $cont=$cont +1;}

            $listaImp = json_encode($lista_aImprimir);
            // dump($query->execute());die();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $maxItemPerPage
            );
            $a = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
            // dump($pagination);die();
            $centrosC = $entityManager->getRepository('AppBundle:departamento')->findAll();
//dump($centrosC);die();
            //$lista_aImprimir=$activos;
            return $this->render(
                'utiles/lista_utiles.html.twig', [
                    'areas' => $area,
                    'usuario' => $this->getUser(),
                    'inventarios' => $accesorio,
                    'lista' => $query->execute(),
                    'imprimir_lista' => '',
                    'pagination' => $pagination,
                    'dep' => $dep, 'listaI' => $listaImp,
                    'centros' => $centrosC,
                    'orden' => $ordenar,
                    'dir' => $direccion,
                ]
            );
        } else if ($this->isGranted('ROLE_JEFE_DEP')) {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipoAssets');
            $nombreArea = $queryArea->execute()[0]->getNombre();
            $area = $nombreArea . $queryArea->execute()[0]->getIdArea();
            $dql = "SELECT t.id,u.idUH,u.descripcion,t.cantidad,t.nombreCosto,t.nombreArea,t.codigoCosto,t.codigoArea FROM 
               AppBundle:utilDetalles t INNER JOIN AppBundle:util u WHERE t.util = u.id and t.nombreArea='$nombreArea' ORDER BY t.id  ";
            $query = $entityManager->createQuery($dql);
            //  dump($query->execute()[0]->getNombre().$query->execute()[0]->getIdArea() );
//            $activos = $repository->createQueryBuilder('tabla2')
//                ->where('tabla2.id_area =:area')
//                ->setParameter('area', $query->execute()[0]->getNombre().$query->execute()[0]->getIdArea()  )
//                ->andWhere('tabla2.activo=:estado')
//                ->setParameter('estado', 1)
//                ->orderBy('tabla2.id_area', 'desc')
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();


            // dump($area);die();
            // $dql = "SELECT t FROM AppBundle:equipoAssets t where t.activo=1 and t.id_area='$area'";
            //   $lista = $entityManager->createQuery($dql);
            // $equipos=$entityManager->getRepository('AppBundle\Entity\equipoAssets')->findBy(['id_costo'=>$query->execute()[0]->getIdArea()]);
            //  dump($query->execute());dump($activos->execute());die();
            $entityManager = $this->getDoctrine()->getManager();
//dump($activos->getResult());die();
            // $idcosto = $activos->execute()[0]->getidCosto();
            $a = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $queryArea->execute()[0]->getIdArea()]);
            //dump($a);die();
            $centrosC = $entityManager->getRepository('AppBundle:departamento')->findBy(['area' => $a]);
            //   dump($centrosC);die();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                $maxItemPerPage
            );
            //  dump($query->execute());die();
            return $this->render(
                'utiles/lista_utiles.html.twig', [
                    'areas' => '',
                    'inventarios' => $accesorio,
                    'lista' => '',
                    'listaI' => $lista_aImprimir,
                    'imprimir_lista' => '',
                    'dep' => $dep,
                    'pagination' => $pagination,
                    'centros' => $centrosC,
                    'usuarios' => $centrosC[0],
                    'orden' => $ordenar,
                    'dir' => $direccion,
                ]

            );

        }
    }

    /**
     * @Route("reportes/lista_utiles_assets", name="lista_utiles_assets")
     * @Method("GET")
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public
    function list_UtilesAssetAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:util')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $applicationRepository,
            $request->query->getInt('page', 1),
            13
        );
        return $this->render(
            'utiles/lista_utilesAssetNom.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("reportes/lista_utiles_assets_detalles", name="lista_utiles_assets_detalles")
     * @Method("GET")
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public
    function list_UtilesDetallesAssetAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:utilDetalles')->findAll();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $applicationRepository,
            $request->query->getInt('page', 1),
            13
        );
        return $this->render(
            'utiles/lista_utilesAssetDetallesNom.html.twig',
            [
                'pagination' => $pagination
            ]
        );
    }

    /**
     * @Route("reportes/cargar_utiles", name="cargar_utiles")
     */
    public
    function cargarUtilesAssetsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        //   $equipoA = $entityManager->getRepository('AppBundle:equipoAssets');

///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            // $sql = "SELECT af.Desc_ActivoFijo, af.Id_Rotulo,af.Id_Ccosto,af.ID_AreaResp,af.Activo,af.Numero_Serie FROM dbo.Activo_Fijo af WHERE af.Id_Ccosto!=999 and af.Activo=1";
            $sql = "SELECT u.Id_UH, u.Desc_UH,u.Fecha_Alta,u.Activo FROM dbo.Util_Tool u WHERE u.Activo=1";
            $query = $coneccionAssets->query($sql);


            // $idcosto = 0;
            //$departamento='';
            if ($query) {
                $utiles = array();
                $em = $this->getDoctrine()->getManager();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $utiles[] = $var;
                }
//                dump($utiles);
//                die();
                foreach ($utiles as $u) {
                    $entity = new util();
                    $idUH = $u['Id_UH'];
                    $descripcion = $u['Desc_UH'];
                    $fecha = $u['Fecha_Alta'];
                    $activo = $u['Activo'];

                    // dump($idcosto);die();
                    set_time_limit(0);

                    $entity->setIdUH($idUH);
                    $entity->setDescripcion($descripcion);
                    $entity->setFechaAlta($fecha);
                    $entity->setActivo($activo);
                    $entityManager = $this->getDoctrine()->getManager();
                    $util = $entityManager->getRepository('AppBundle:util')->findOneBy(['idUH' => $idUH]);
                    if ($util == null) {
                        $em->persist($entity);
                        $em->flush();
                    }
                }
                $utilLista = $entityManager->getRepository('AppBundle:util')->findAll();
            }
            $coneccionAssets = null;
            //  $cant=0;

            //   $cant=$cant+1;
            // dump($dep);die();
        } catch (\PDOException $e) {
            ("No se conecta con el servidor! - " . $e->getMessage());
        }

        try {
            $coneccionAssets2 = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );
            $sqlDetalles = "SELECT ud.Id_UH, ud.Id_Ccosto idcosto,ud.Id_AreaResponsabilidad,ud.Desc_Ccosto,ud.Desc_AreaResponsabilidad,ud.Cantidad FROM dbo.Util_Tool_Detalles ud ";
            $query2 = $coneccionAssets2->query($sqlDetalles);
            if ($query2) {
                $utilesDetalles = array();
                $em = $this->getDoctrine()->getManager();
                while ($var2 = $query2->fetch(\PDO::FETCH_ASSOC)) {
                    $utilesDetalles[] = $var2;
                }

            }
            foreach ($utilesDetalles as $ud) {
                $entity = new utilDetalles();
                $idUH = $ud['Id_UH'];
                $codigoCosto = $ud['idcosto'];
                $codigoArea = $ud['Id_AreaResponsabilidad'];
                $decCosto = $ud['Desc_Ccosto'];
                $descArea = $ud['Desc_AreaResponsabilidad'];
                $cantidad = $ud['Cantidad'];


                // dump($idcosto);die();
                set_time_limit(0);

                $util = $entityManager->getRepository('AppBundle:util')->findOneBy(['idUH' => $idUH]);
                $entity->setUtil($util);
                $entity->setCodigoCosto($codigoCosto);
                $entity->setCodigoArea($codigoArea);
                $entity->setNombreCosto($decCosto);
                $entity->setNombreArea($descArea);
                $entity->setCantidad($cantidad);
                $entityManager = $this->getDoctrine()->getManager();
                $utilsDetalles = $entityManager->getRepository('AppBundle:utilDetalles')->findAll();
                //dump($utilsDetalles);die();
                if ($utilsDetalles != []) {
                    $repository = $this->getDoctrine()
                        ->getRepository('AppBundle:utilDetalles');
                    $utilDet = $repository->createQueryBuilder('ud')
                        ->andWhere('ud.util=:util')
                        ->setParameter('util', $util)
                        ->andWhere('ud.codigoArea=:codigoA')
                        ->setParameter('codigoA', $codigoArea)
                        ->andWhere('ud.codigoCosto=:codigoC')
                        ->setParameter('codigoC', $codigoCosto)
                        ->andWhere('ud.nombreArea=:nA')
                        ->setParameter('nA', $descArea)
                        ->andWhere('ud.nombreCosto=:nC')
                        ->setParameter('nC', $decCosto)
                        ->andWhere('ud.cantidad=:cant')
                        ->setParameter('cant', $cantidad)
                        ->getQuery()->execute();

                    if ($utilDet == null) {
                        dump('no existe');
                        $em->persist($entity);
                        $em->flush();
                    } else {
                        dump('existe');
                    }
                } else {
                    $em->persist($entity);
                    $em->flush();
                }


            }
            $utilsDetalles = $entityManager->getRepository('AppBundle:utilDetalles')->findAll();
            dump($sqlDetalles);
            dump($utilsDetalles);
            die();

        } catch (\PDOException $e) {
            ("No se conecta con el servidor! - " . $e->getMessage());
        }

        $listaE = $equipoA->findAll();
        dump($result[0]['Desc_Ccosto']);
        die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $aset,
            $request->query->getInt('page', 1)
        );
        // dump($utilLista);die();
        return $this->render(
            'utiles/lista_utilesAssetNom.html.twig',
            [
                'lista' => $utilsDetalles,

                //'inventarios' => $inventarios,
                //"filters" => $this->filters,
                "pagination" => ''
            ]
        );
    }

    /**
     * @Route("/reportes/utiles/filtrar_utiles_assets2/{dep}",name="filtra_utiles")
     */
    public function filtraUtiles2Action(Request $request, $dep, $sort = null, $direction = null)
    {
        $ordenar = $sort;
        $direccion = $direction;
        if ($request->request == null) {
            $nombre_departamento = $request->query->get('usuarios')[0];
            //  dump($this->get('aquii'));
        } else {
            $nombre_departamento = $request->request->get('usuarios')[0];
        }
        if ($request->request->get('sort')) {
            $ordenar = $request->request->get('sort')[0];
        }
        if ($request->request->get('direction')) {
            $direccion = $request->request->get('direction')[0];
        }
//        $usuarios=new  ArrayCollection();
//        $usuarios[0]=$nombre_departamento;
//dump($dep);die();
        $entityManager = $this->getDoctrine()->getManager();

        $em = $this->get('doctrine.orm.entity_manager');
        // $idArea = $em->getRepository('AppBundle:departamento')->findBy(['name' => $nombre_departamento])[0]->getArea()->getIdArea();
        $centrosC = $em->getRepository('AppBundle:departamento')->findBy(['name' => $nombre_departamento]);
        //$idCostoc = $centrosC[0]->getIdCosto();
        // dump($request);dump($dep);dump($request->request->get('usuarios')[0]);die();
        $accesorio = '';
///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
        $database = "RETINOSIS";
        $uid = 'user_assetsp';
        $pwd = '2020*Fuerza';
        try {
            $coneccionAssets = new \PDO(
                "sqlsrv:server=$serverName;Database=$database",
                $uid,
                $pwd,
                array(
                    //PDO::ATTR_PERSISTENT => true,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                )
            );

            /**
             * Consulta para obtener los centros de costos
             */
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                }
            }

            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }

        $num = $request->get('numI');
        // dump($num);
        // die();
        //$estado_estacion = $request->get('estado');
        // dump($estado_estacion);die();
        if ($num == '') {
            $this->addFlash('alerta', 'Usted debe escribir el numero de inventario, para buscar');
        }
        if ($num != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $util = $entityManager->getRepository('AppBundle:util')->findOneBy(['idUH' => $num]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:utilDetalles');
            $query = $repository->createQueryBuilder('tabla')
                ->andWhere('tabla.util =:utilID')
                ->setParameter('utilID', $util->getId())
                ->andWhere('tabla.nombreCosto=:dep')
                ->setParameter('dep', $dep)
                ->orderBy('tabla.id', 'desc')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();

            $qb = $em->createQueryBuilder();
            $qb->select('t.id', 'u.idUH', 'u.descripcion', 't.cantidad', 't.nombreCosto', 't.nombreArea', 't.codigoCosto', 't.codigoArea');
            $qb->from('AppBundle:utilDetalles', 't');
            $qb->innerJoin('AppBundle:util', 'u');
            $qb->where('t.util=u.id');
            $qb->andWhere('t.util =:utilID');
            $qb->setParameter('utilID', $util->getId());
            $qb->andWhere('t.nombreCosto=:dep');
            $qb->setParameter('dep', $dep);
            $qb->orderBy('t.id', 'desc');
            $qb->getQuery();


         //  dump( $qb->getQuery()->execute());die();
            //  $estacion = $applicationRepository;
            $em = $this->get('doctrine.orm.entity_manager');
//             dump($applicationRepository);
//             die();
//            $dql = "SELECT a FROM AppBundle:utilDetalles a WHERE a.numInventario = " . $num . " ";
//            $query = $em->createQuery($dql);
//       dump($dql);
//        die();
            if ($query->execute() == null) {
                $this->addFlash('alert', 'No existe util con el codigo especificado');
            } else {
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $qb->getQuery(),
                    $request->query->getInt('page', 1)
                );
                $are = $entityManager->getRepository('AppBundle:area')->findOneBy(['nombre' => $dep]);
//                dump($pagination);
//                die();
                return $this->render('utiles/lista_utiles.html.twig', ['pagination' => $pagination, 'areas' => $area, 'componente' => 'backup',
                    'inventarios' => $accesorio, 'dep' => $dep, 'area' => $are, 'centros' => '', 'listI' => $query->execute(), 'orden' => $ordenar, 'dir' => $direccion,
                    'lista' => $query->execute()]);

            }
        }


        return $this->redirectToRoute('lista_utiles');
    }

    /**
     * @Route("reportes/utiles/listado",name="imprimirUtiles")
     * @param Request
     *
     * @return Response
     */
    public function imprimirUtilesAction(Request $request)
    {
        $list = $request->query->get('lista');
        $lista = json_decode($list);
        // $request->setMethod(Post::class);
        $orden = null;
        $dir = null;
        $dep = null;
        $codigoArea = null;

        if ($request->query->get('orden')) {
            $orden = $request->query->get('orden');
        }
        if ($request->query->get('dir')) {
            $dir = $request->query->get('dir');
        }
        if ($request->query->get('dep')) {
            $dep = $request->query->get('dep');
        }
        if ($request->query->get('area')) {
            $codigoArea = $request->query->get('area');
        }
        //  dump($request);die();
        if ($orden != null) {
            $em = $this->get('doctrine.orm.entity_manager');
            $qb = $em->createQueryBuilder();
            $qb->select('t.id', 'u.idUH', 'u.descripcion', 't.cantidad', 't.nombreCosto', 't.nombreArea', 't.codigoCosto', 't.codigoArea');
            $qb->from('AppBundle:utilDetalles', 't');
            $qb->innerJoin('AppBundle:util', 'u');
            $qb->where('t.util=u.id');
            $qb->andWhere('t.codigoCosto=:codigoC');
            $qb->setParameter('codigoC', $dep);
            $qb->andWhere('t.codigoArea=:area');
            $qb->setParameter('area', $codigoArea);
            $qb->orderBy($orden, $dir);
        } else {
            $em = $this->get('doctrine.orm.entity_manager');
            $qb = $em->createQueryBuilder();
            $qb->select('t.id', 'u.idUH', 'u.descripcion', 't.cantidad', 't.nombreCosto', 't.nombreArea', 't.codigoCosto', 't.codigoArea');
            $qb->from('AppBundle:utilDetalles', 't');
            $qb->innerJoin('AppBundle:util', 'u');
            $qb->where('t.util=u.id');
            $qb->andWhere('t.codigoCosto=:codigoC');
            $qb->setParameter('codigoC', $dep);
            $qb->andWhere('t.codigoArea=:area');
            $qb->setParameter('area', $codigoArea);
        }
//        $idCostoE = substr($id, -3);
//        $idAreaE = explode(' ', $idCosto);
        // $idAreaEReal=array_slice($idAreaE, 3)[36];
        // str_split($idAreaE, ' ');
        //$area=$em->getRepository('AppBundle:area')->findBy(['id_area'=>$idAreaEReal]);
        //  $nombreCodigo="".$area[0]->getNombre().$idAreaEReal."";
        //dump($idCosto);die();
        // dump(array_slice($idAreaE, 3)[36]);die();

//        if ($id == '' and $idCosto == '') {
////            $dql = "SELECT  i FROM AppBundle:equipoAssets i
////           WHERE i.activo=1 ";
//
//            $repository = $this->getDoctrine()
//                ->getRepository('AppBundle:equipoAssets');
//            $dql = $repository->createQueryBuilder('tabla')
//                ->where('tabla.activo = :a')
//                ->setParameter('a', 1)
//                ->getQuery();
//
////        } else {
////            if ($idCosto == '' ) {
//////                $dql = "SELECT  i FROM AppBundle:equipoAssets i
//////           WHERE i.activo=1 and i.id_costo = " . $idCostoE . "
//////         ";
////                $repository = $this->getDoctrine()
////                ->getRepository('AppBundle:equipoAssets');
////                $dql = $repository->createQueryBuilder('tabla')
////                ->where('tabla.activo = :a')
////                // ->andWhere('tabla.inventario =: idE')
////                ->setParameter('a', 1)
////                ->andWhere('tabla.id_costo = :id')
////                ->setParameter('id', $idCostoE)
////                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
////                ->getQuery();
//        } else {
////                $dql = "SELECT  i FROM AppBundle:equipoAssets i
////
////           WHERE i.activo=1 and i.id_costo = " . $id . " and i.id_area=" . $idCosto . "
////         ";
//            $repository = $this->getDoctrine()
//                ->getRepository('AppBundle:equipoAssets');
//            $dql = $repository->createQueryBuilder('tabla')
//                ->where('tabla.activo = :a')
//                // ->andWhere('tabla.inventario =: idE')
//                ->setParameter('a', 1)
//                ->andWhere('tabla.id_costo = :id')
//                ->setParameter('id', $id)
//                ->andWhere('tabla.id_area = :idA')
//                ->setParameter('idA', $idCosto)
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();
//        }

        // dump($qb->getQuery()->execute());die();

        //$query = $em->createQuery($dql);
        //  dump($dql->execute());die();
        $lista = $qb->getQuery()->execute();
        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('post', true);
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('utiles/listado.html.twig', ['lista' => $lista]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html, array(
                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
            )), 200, array(
                'title' => 'Lista de activos',
                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'viewport-size' => '1027x768',
                'margin-left' => '5mm',
                'margin-right' => '10mm',
                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }

}
