<?php

namespace AppBundle\Controller;

use AppBundle\Entity\centroCosto;
use AppBundle\Entity\equipoAssets;
use AppBundle\Entity\incidencia;
use AppBundle\Entity\movimiento;
use AppBundle\Entity\movimientoI_AF;
use AppBundle\Entity\tipo;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class activosFijosController extends Controller
{
//    public function indexAction($name)
//    {
//        return $this->render('', array('name' => $name));
//    }
    /**
     * @Route("reportes/lista_activos_fijos", name="lista_activos_fijos")
     */
    public function listaActivosFijosAction(Request $request, $maxItemPerPage = 10)
    {
//r
        // dump($request);
        $ordenar = null;
        $direccion = null;
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

//           dump($ordenar);
//         die();

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
        $serverName = "192.168.107.20";
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
        $query = $repository->createQueryBuilder('tabla')
            ->andWhere('tabla.nombre =:area')
            ->setParameter('area', $dep)
//            ->andWhere('tabla.user=:usuario_actual')
//            ->setParameter('usuario_actual', $this->getUser()->getUsername())
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();

        if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_TECNICO') or $this->isGranted('ROLE_AFT')) {
//            $applicationRepository = $entityManager->getRepository('AppBundle:equipoAssets');
//            $activos = $applicationRepository->findBy(['activo' => 1]);
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipoAssets');
            $activos = $repository->createQueryBuilder('t')
                ->andWhere('t.activo=:estado')
                ->setParameter('estado', 1)
                ->orderBy('t.id', 'desc')
                ->getQuery();

            $dql = "SELECT t FROM AppBundle:equipoAssets t where t.activo=1 ";
            $lista = $entityManager->createQuery($dql);
            $idcosto = $activos->execute()[0]->getIdArea();
            //$idcosto = $query->execute()[0]->getIdArea();
            // $lista = $entityManager->getRepository('AppBundle:equipoAssets','t')->findAll();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $lista,
                $request->query->getInt('page', 1),
                $maxItemPerPage
            );
            $a = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
            //dump($lista);die();
            $centrosC = $entityManager->getRepository('AppBundle:departamento')->findAll();
//dump($pagination);die();
            return $this->render(
                'activos_fijos/lista_activos_fijos.html.twig', [
                    'areas' => $area,
                    'orden' => $ordenar,
                    'dir' => $direccion,
                    'jsonList' => '',
                    'componente' => 'backup',
                    'inventarios' => $accesorio,
                    'lista' => $lista->execute(),
                    'imprimir_lista' => '',
                    'pagination' => $pagination,
                    'dep' => $dep,
                    'centros' => $centrosC,
                    'entity'=>$this->getUser()
                ]
            );
        } else if ($this->isGranted('ROLE_JEFE_DEP')) {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipoAssets');
            //  dump($query->execute()[0]->getNombre().$query->execute()[0]->getIdArea() );
//            $activos = $repository->createQueryBuilder('tabla2')
//                ->where('tabla2.id_area =:area')
//                ->setParameter('area', $query->execute()[0]->getNombre().$query->execute()[0]->getIdArea()  )
//                ->andWhere('tabla2.activo=:estado')
//                ->setParameter('estado', 1)
//                ->orderBy('tabla2.id_area', 'desc')
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();
            $area = $query->execute()[0]->getNombre() . $query->execute()[0]->getIdArea();
            $dql = "SELECT t FROM AppBundle:equipoAssets t where t.activo=1 and t.id_area='$area'";
            $lista = $entityManager->createQuery($dql);
            // $equipos=$entityManager->getRepository('AppBundle\Entity\equipoAssets')->findBy(['id_costo'=>$query->execute()[0]->getIdArea()]);
            //  dump($query->execute());dump($activos->execute());die();
            $entityManager = $this->getDoctrine()->getManager();
//dump($activos->getResult());die();
            // $idcosto = $activos->execute()[0]->getidCosto();
            $a = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $query->execute()[0]->getIdArea()]);
            //dump($a);die();
            $centrosC = $entityManager->getRepository('AppBundle:departamento')->findBy(['area' => $a]);
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $lista,
                $request->query->getInt('page', 1),
                $maxItemPerPage
            );

//            dump($a);dump($centrosC);dump($pagination);
//          //  dump($activos->execute());
//            die();
            return $this->render(
                'activos_fijos/lista_activos_fijos.html.twig', [
                    'areas' => '',
                    'jsonList' => '',
                    'orden' => $ordenar,
                    'dir' => $direccion,
                    'inventarios' => $accesorio,
                    'lista' => $lista->execute(),
                    'imprimir_lista' => '',
                    'dep' => $dep,
                    'pagination' => $pagination,
                    'centros' => $centrosC,
                    'entity'=>$this->getUser()
                ]

            );
        } else {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipoAssets');
            $activos = $repository->createQueryBuilder('tabla2')
                ->andWhere('tabla2.id_costo =:area')
                ->setParameter('area', $query->execute()[0]->getIdArea())
                ->andWhere('tabla2.activo=:estado')
                ->setParameter('estado', 1)
                ->orderBy('tabla2.id', 'desc')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            // $equipos=$entityManager->getRepository('AppBundle\Entity\equipoAssets')->findBy(['id_costo'=>$query->execute()[0]->getIdArea()]);
            // dump($query->execute());dump($equipos);die();
            $entityManager = $this->getDoctrine()->getManager();
//dump($activos->getResult());die();
            $idcosto = $activos->execute()[0]->getidCosto();
            $a = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
            //dump($a);die();
            $centrosC = $entityManager->getRepository('AppBundle:departamento')->findBy(['area' => $a]);
            return $this->render(
                'activos_fijos/lista_activos_fijos.html.twig', [
                    'areas' => '',
                    'inventarios' => $accesorio,
                    'lista' => '',
                    'jsonList' => '',
                    'orden' => $ordenar,
                    'dir' => $direccion,
                    'imprimir_lista' => '',
                    'dep' => $dep,
                    'pagination' => '',
                    'centros' => $centrosC
                ]

            );

        }


//        $a = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
//        //dump($a);die();
//        $centrosC = $entityManager->getRepository('AppBundle:departamento')->findBy(['area' => $a]);
//        //dump($centrosC);die();
//        // dump($usuario_ums[0]['dep']);die();
//        return $this->render(
//            'activos_fijos/lista_activos_fijos.html.twig', [
//                'areas' => $area,
//                'componente' => 'backup',
//                'inventarios' => $accesorio,
//                'lista' => '',
//                'imprimir_lista' => '',
//                'pagination' => $pagination,
//                'dep' => $dep,
//                'centros' => $centrosC
//
//
//            ]
//
//        );
    }

    /**
     * @Route("reportes/activos_fijos/{id_equi}/{id_area}/traslado", name="traslado_activo")
     */
    public function trasladoActivoFijoAction(Request $request, $id_equi, $id_area, \Swift_Mailer $mailer)
    {
        $movimiento = new movimientoI_AF();
        $movimiento->setTecnico($this->getUser());
        $tipoForm = $this->createForm('AppBundle\Form\trasladoAFFormType', $movimiento);
        //$equipo_min = strtolower($name);
        $entityManager = $this->getDoctrine()->getManager();

        $activoF = $entityManager->getRepository('AppBundle:equipoAssets')->find($id_equi);
        $idCosto = $request->query->get('id_costo');
        $id_costo = substr($activoF->getIdCosto(), -3, 3);
        $id_dep = substr($activoF->getIdArea(), -10, 3);
      if($id_dep=="   "){
          $id_dep = substr($activoF->getIdArea(), -3, 3);
      }
        $dep = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $id_costo]);
        $area = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $id_dep]);
        // $totalC=strlen("".$id_area);
//        dump($area);
//        dump($activoF->getIdArea());die();
//        dump($dep);
     //   dump($id_dep);die();


        // dump($area);die();
        // $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id_inve);
        //$incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_inci);

        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {
            $tipo = $tipoForm->getData();
            //Creando la incidencia
            $incidencia = new incidencia();
            $incidencia->setTipo('Movimiento Activo Fijo');
            $incidencia->setUser($this->getUser()->getUsername());
            // $dpto = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_equi]);
            $incidencia->setDpto($dep[0]->getName());
            $incidencia->setAsunto('Movimiento Activo Fijo');
            $incidencia->setResumen('Activo fijo que cambia de departamento');
            $incidencia->setFecha(new \DateTime("now"));
            $incidencia->setRespuesta('Traslado Interno');
            $incidencia->setInventario(null);
            $incidencia->setTipoMov('Traslado Interno');
            $incidencia->setEstado('Traslado');
            $incidencia->setEstadoMovimiento('Pendiente');
            $incidencia->setAsesorio($activoF->getDescripcion());
            $incidencia->setNumInventario($activoF->getNumInventario());
            $incidencia->setIdE($activoF->getId());
            $incidencia->setTecAsignado("--");
            $incidencia->setCorreo($this->getUser()->getEmail());
            $incidencia->setFechaA(new \DateTime("now"));

            $tipo->setInventario(null);

            // $tipo->setTipoMovimiento("Traslado Interno");
            //  $tipo->setIncidencia($incidencia);
            $tipo->setPeriferico($activoF->getDescripcion());
            $fecha_actual = new \DateTime("now");
            $tipo->setFecha($fecha_actual);


            //  $ram = $entityManager->getRepository('AppBundle:ram')->findOneBy(['inventario' => $id_inve]);
///poner el equipo como roto
            //$eqipo->setEstado('Activo');
            //  dump($dep);die();
            $activoF->setIdArea($area[0]->getNombre() . " " . $area[0]->getIdArea());
            $activoF->setIdCosto($dep[0]->getName() . " " . $dep[0]->getIdCosto());
            $entityManager->persist($incidencia);
            $tipo->setIncidencia($incidencia);
            $entityManager->persist($tipo);
            $entityManager->flush();

            // dump($incidencia);die();
            //Enviar correo
            $email = $this->getUser()->getEmail();
            $message = (new \Swift_Message('Sistema Control Reportes'))
                ->setFrom('reportes@retina.sld.cu')
                ->setTo($email)
                ->setCc('lisandra.hernandez@retina.sld.cu')//aqui va la lista de correo
                ->setBody($this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration_movimientoAFT.html.twig',
                    ['estado' => $incidencia->getEstado(), 'tipo' => $incidencia->getTipo(), 'incidencia' => $incidencia,
                        'mov' => $movimiento]
                ),
                    'text/html'

                );

            $mailer->send($message);
            $this->addFlash('success', 'Traslado Interno Realizado Correctamente');
            return $this->redirectToRoute('incidencia_movimientoAFT_ver', ['id' => $incidencia->getId()]);

        }
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        //$departamentos
        return $this->render('incidencia/traslado_activoF.html.twig', ['movimientoForm' => $tipoForm->createView(),
            'nombre' => $activoF->getDescripcion(),
            'asesorio' => $activoF, 'departamento' => $dep, 'area' => $area[0],
            'entity' => $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllUserUms(),
            'usuarios' => $usuarios]);

    }

    /**
     * @Route("/reportes/activos_fijos/filter",name="activoF_filter")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function filterActivoAction(Request $request)
    {
        //$p = $this->get('knp_paginator')->paginate();
        $entityManager = $this->getDoctrine()->getManager();


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

        //$area = $request->request->get('usuarios');
        $dep = $request->request->get('usuarios');
        $estacion = $request->request->get('estaciones');
        $numInv = $request->request->get('tipo');
        $idCostoS = $request->request->get('costos');
        //  $p=$request->request->get('pagination');
        //dump($request);die();
        $entityManager = $this->getDoctrine()->getManager();
        //$select = $request->request->get('componente');

        if ($dep == '') {
            $this->addFlash('alerta', 'Usted debe llenar los filtros para buscar');
        }


        if ($dep != '') {
//            //  dump('holaaaaaaa');die();
            $em = $this->get('doctrine.orm.entity_manager');
            $dql = "SELECT i.descripcion,i.serie,i.numInventario,i.id_area,i.id_costo,i.activo,i.id FROM AppBundle:equipoAssets i
           WHERE i.activo=1 and i.id_costo = " . $dep[0] . "
         ";
            //dump($dep);  dump($request);
//            $em = $this->get('doctrine.orm.entity_manager');
//            $repository = $this->getDoctrine()
//                ->getRepository('AppBundle:equipoAssets');
//            $query1 = $repository->createQueryBuilder('tabla')
//                ->where('tabla.activo = :a')
//                // ->andWhere('tabla.inventario =: idE')
//                ->setParameter('a', 1)
//                ->andWhere('tabla.id_costo = :id')
//                ->setParameter('id', $dep[0])
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();

            $query = $em->createQuery($dql);
            $a = $query->execute();
//          dump($a[0]);
//            die();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1)
            );
//            dump($a);
//            dump($pagination);
//            die();
            $p = $pagination;
//            dump($pagination);
//            dump($a);
//            die();
            if ($a == []) {
                $imprimir = '';
            } else {
                $imprimir = 'si';
            }

            return $this->render('activos_fijos/lista_activos_fijos.html.twig', [
                "areas" => $area,
                'imprimir_lista' => $imprimir,
                "pagination" => $pagination,
                'lista' => $a,

            ]);

        }


        $this->addFlash('alert', 'No existen activos en el centro de costo seleccionado');
        return $this->redirectToRoute('lista_activos_fijos');
    }

    /**
     * @Route("/reportes/equipos/filtrar_equipos_historial/{lista}",name="busca_historial_activo")
     */
    public function filtraActivoHistorialAction(Request $request, $lista)
    {
        //  dump($lista);die();
        $entityManager = $this->getDoctrine()->getManager();

        $em = $this->get('doctrine.orm.entity_manager');

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
            $applicationRepository = $entityManager->getRepository('AppBundle:equipoAssets')->findOneBy(['numInventario' => $num]);
            //  $estacion = $applicationRepository;
            $em = $this->get('doctrine.orm.entity_manager');
//             dump($applicationRepository);
//             die();
            $dql = "SELECT a,af.areaDestino FROM AppBundle:incidencia a INNER JOIN AppBundle:movimientoI_AF af
                 WHERE a.num_inventario = " . $num . " and af.incidencia=a.id and a.tipo='Movimiento Activo Fijo' ";
            $query = $em->createQuery($dql);
//       dump($dql);
//        die();
            if ($query->execute() == null) {
                $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
            } else {
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $query->execute(),
                    $request->query->getInt('page', 1), 1
                );
                //   $are = $entityManager->getRepository('AppBundle:area')->findOneBy(['nombre' => $dep]);

                $l = json_decode($lista);

                //  dump($l);die();
                return $this->render('activos_fijos/historial_movimientos.html.twig', ['pagination' => $pagination, 'areas' => '', 'componente' => 'backup',
                    'inventarios' => '', 'dep' => '', 'area' => '', 'centros' => '',
                    'lista' => $applicationRepository, 'lista_historial' => $l]);

            }
        }


        return $this->redirectToRoute('lista_activos_fijos');
    }


    /**
     * @Route("reportes/activos_fijos/lista/{id}/{idCosto}",name="imprimir_activos")
     */
    public function imprimirActivosAction(Request $request, $id = '', $idCosto = '')
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $idCostoE = substr($id, -3);
        $idAreaE = explode(' ', $idCosto);
        // $idAreaEReal=array_slice($idAreaE, 3)[36];
        // str_split($idAreaE, ' ');
        //$area=$em->getRepository('AppBundle:area')->findBy(['id_area'=>$idAreaEReal]);
        //  $nombreCodigo="".$area[0]->getNombre().$idAreaEReal."";
        //dump($idCosto);die();
        // dump(array_slice($idAreaE, 3)[36]);die();

        if ($id == '' and $idCosto == '') {
//            $dql = "SELECT  i FROM AppBundle:equipoAssets i
//           WHERE i.activo=1 ";

            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipoAssets');
            $dql = $repository->createQueryBuilder('tabla')
                ->where('tabla.activo = :a')
                ->setParameter('a', 1)
                ->getQuery();

//        } else {
//            if ($idCosto == '' ) {
////                $dql = "SELECT  i FROM AppBundle:equipoAssets i
////           WHERE i.activo=1 and i.id_costo = " . $idCostoE . "
////         ";
//                $repository = $this->getDoctrine()
//                ->getRepository('AppBundle:equipoAssets');
//                $dql = $repository->createQueryBuilder('tabla')
//                ->where('tabla.activo = :a')
//                // ->andWhere('tabla.inventario =: idE')
//                ->setParameter('a', 1)
//                ->andWhere('tabla.id_costo = :id')
//                ->setParameter('id', $idCostoE)
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();
        } else {
//                $dql = "SELECT  i FROM AppBundle:equipoAssets i
//
//           WHERE i.activo=1 and i.id_costo = " . $id . " and i.id_area=" . $idCosto . "
//         ";
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipoAssets');
            $dql = $repository->createQueryBuilder('tabla')
                ->where('tabla.activo = :a')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('a', 1)
                ->andWhere('tabla.id_costo = :id')
                ->setParameter('id', $id)
                ->andWhere('tabla.id_area = :idA')
                ->setParameter('idA', $idCosto)
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
        }

        // dump($dql);die();

        //$query = $em->createQuery($dql);
        //  dump($dql->execute());die();
        $lista = $dql->execute();
        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('activos_fijos/listado.html.twig', ['lista' => $lista]);

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


    /**
     * @Route("reportes/activos_fijos/lista2",name="imprimiract")
     */
    public function imprimirActivos2Action(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $orden = null;
        $dir = null;
        $dep = null;
        if ($request->query->get('orden')) {
            $orden = $request->query->get('orden');
        }
        if ($request->query->get('dir')) {
            $dir = $request->query->get('dir');
        }
        if ($request->query->get('dep')) {
            $dep = $request->query->get('dep');
        }
        $datos = $request->query->get('lista');
        //   dump($request);die();
        $qb = $em->createQueryBuilder();
        $qb->select('t');
        $qb->from('AppBundle:equipoAssets', 't');
        $qb->where('t.id_costo=:depart');
        $qb->setParameter('depart', $dep);
        $qb->andWhere('t.activo=:act');
        $qb->setParameter('act', 1);
        $qb->orderBy($orden, $dir);
//        $qb->andWhere('t.id_area=:area');
//        $qb->setParameter('area', $area[0]->getNombre() . $area[0]->getIdArea());
//dump($request);
//dump($qb->getQuery());die();
        // $lista = json_decode($datos);

        $lista = $qb->getQuery()->execute();


//        $idCostoE = substr($id, -3);
//        $idAreaE = explode(' ', $idCosto);
        // $idAreaEReal=array_slice($idAreaE, 3)[36];
        // str_split($idAreaE, ' ');
        //$area=$em->getRepository('AppBundle:area')->findBy(['id_area'=>$idAreaEReal]);
        //  $nombreCodigo="".$area[0]->getNombre().$idAreaEReal."";
//        dump(json_decode($datos));
//        die();
        // dump(array_slice($idAreaE, 3)[36]);die();


        $snappy = $this->get('knp_snappy.pdf');
      //  $snappy->setOption()
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('activos_fijos/listado.html.twig', ['lista' => $lista]);
     //   $snappy->setOption('page-width', 50);
        $filename = 'reporte';


        //  $html = $this->renderView('activos_fijos/listado.html.twig', array('lista' => $lista));

        return new Response(

            $snappy->getOutputFromHtml($html, array(
                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
            )), 200, array(
                'title' => 'Lista de activos',
                'Content-Type' => 'application/pdf',
//                'enable-javascript' => true,
//              //  'page-height' =>  '279mm',
//               // 'page-width' => '216 mm',
//               // 'footer-spacing' => 5,
//            //   'viewport-size' => '800x600',
//                'page-size'=> 'Us Letter',
//                'margin-left' => '10mm',
//                'margin-right' => '10mm',
//                'margin-top' => '30mm',
//                //'dpi'=>'96',
//              //  'disable-smart-shrinking',
//                'no-outline'=>'true',
//                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }


//    /**
//     * @Route("reportes/activos_fijos/lista/{id}/{idCosto}",name="imprimir_activos")
//     */
//    public function imprimirActivosAction(Request $request, $id = '', $idCosto = '',$lista=null)
//    {
//        $em = $this->get('doctrine.orm.entity_manager');
//        $idCostoE = substr($id, -3);
//        $idAreaE = explode(' ', $idCosto);
//        dump($request);die();
//        // $idAreaEReal=array_slice($idAreaE, 3)[36];
//        // str_split($idAreaE, ' ');
//        //$area=$em->getRepository('AppBundle:area')->findBy(['id_area'=>$idAreaEReal]);
//        //  $nombreCodigo="".$area[0]->getNombre().$idAreaEReal."";
//        //dump($idCosto);die();
//        // dump(array_slice($idAreaE, 3)[36]);die();
//
////        if ($id == '' and $idCosto == '') {
//////            $dql = "SELECT  i FROM AppBundle:equipoAssets i
//////           WHERE i.activo=1 ";
////
////            $repository = $this->getDoctrine()
////                ->getRepository('AppBundle:equipoAssets');
////            $dql = $repository->createQueryBuilder('tabla')
////                ->where('tabla.activo = :a')
////                ->setParameter('a', 1)
////                ->getQuery();
////
//////        } else {
//////            if ($idCosto == '' ) {
////////                $dql = "SELECT  i FROM AppBundle:equipoAssets i
////////           WHERE i.activo=1 and i.id_costo = " . $idCostoE . "
////////         ";
//////                $repository = $this->getDoctrine()
//////                ->getRepository('AppBundle:equipoAssets');
//////                $dql = $repository->createQueryBuilder('tabla')
//////                ->where('tabla.activo = :a')
//////                // ->andWhere('tabla.inventario =: idE')
//////                ->setParameter('a', 1)
//////                ->andWhere('tabla.id_costo = :id')
//////                ->setParameter('id', $idCostoE)
//////                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//////                ->getQuery();
////        } else {
//////                $dql = "SELECT  i FROM AppBundle:equipoAssets i
//////
//////           WHERE i.activo=1 and i.id_costo = " . $id . " and i.id_area=" . $idCosto . "
//////         ";
////            $repository = $this->getDoctrine()
////                ->getRepository('AppBundle:equipoAssets');
////            $dql = $repository->createQueryBuilder('tabla')
////                ->where('tabla.activo = :a')
////                // ->andWhere('tabla.inventario =: idE')
////                ->setParameter('a', 1)
////                ->andWhere('tabla.id_costo = :id')
////                ->setParameter('id', $id)
////                ->andWhere('tabla.id_area = :idA')
////                ->setParameter('idA', $idCosto)
////                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
////                ->getQuery();
////        }
//
//        // dump($dql);die();
//
//        //$query = $em->createQuery($dql);
//        //  dump($dql->execute());die();
//       // $lista = $dql->execute();
//        $snappy = $this->get('knp_snappy.pdf');
//        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
//        $html = $this->renderView('activos_fijos/listado.html.twig', ['lista' => $listaOrdenada]);
//
//        $filename = 'reporte';
//
//        return new Response(
//
//            $snappy->getOutputFromHtml($html, array(
//                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
//            )), 200, array(
//                'title' => 'Lista de activos',
//                'Content-Type' => 'application/pdf',
//                'enable-javascript' => true,
//
//                'viewport-size' => '1027x768',
//                'margin-left' => '5mm',
//                'margin-right' => '10mm',
//                'margin-top' => '30mm',
//
//                'margin-bottom' => '25mm',
//                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
//
//            )
//
//        );
//    }
//


    /**
     * @Route("reportes/movimientos/lista_incidencias/{maxItemPerPage}", name="lista_incidencias_movimiento")
     */
    public function listaInciMovAction(Request $request, $maxItemPerPage = 10)
    {
        $estados = [];
        if ($request->query->get('estados')) {
            $estados = $request->query->get('estados');
        }


        //  dump($estados);die();
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:incidencia');
        //$mov='Movimiento Activo Fijo';
        $inventarios = $applicationRepository->findAll();
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_AFT')) {
//            $query = $repository->createQueryBuilder('tabla')
//                ->select('tabla', 'mov.areaDestino','mov.areaEntrega')
//              //  ->innerJoin('mov.incidencia', 'mov', Expr\Join::WITH, $qb->expr()->eq('u.status_id', '?1'))
//               ->innerJoin('AppBundle:movimientoI_AF', 'mov', null,'mov.incidencia=:tabla.id')
//                ->where('tabla.tipo =:tipo_mov')
//                ->setParameter('tipo_mov', 'Movimiento Activo Fijo')
////                ->andWhere('tabla.dpto=:dpto')
////                ->setParameter('dpto', 'mov.areaEntrega')
//                ->orderBy('tabla.id', 'desc')
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();

            $qb = $entityManager->createQueryBuilder();
            $qb->select(array('i', 'mov.areaDestino', 'i.num_inventario', 'i.dpto', 'i.id', 'i.estadoMovimiento'))
                ->from('AppBundle\Entity\incidencia', 'i')
                ->leftJoin('AppBundle\Entity\movimientoI_AF', 'mov', \Doctrine\ORM\Query\Expr\Join::WITH, 'i = mov.incidencia')
                ->where("i.tipo = 'Movimiento Activo Fijo' ")
                ->orderBy('i.id', 'DESC');

            $query = $qb->getQuery();
            $results = $query->getResult();
            // dump($results);die();
        }
//        else if($this->getUser()->getRol() == 'ROLE_AFT' or $this->getUser()->getRol() == 'ROLE_JEFE_DEP'){
//
//        }

        else {
//            $query = $repository->createQueryBuilder('tabla')
//                ->andWhere('tabla.tipo =:mov')
//                ->setParameter('mov', 'Movimiento Activo Fijo')
////            ->andWhere('tabla.user=:usuario_actual')
////            ->setParameter('usuario_actual', $this->getUser()->getUsername())
//                ->andWhere('tabla.user=:usuario_actual')
//                ->setParameter('usuario_actual', $this->getUser()->getUsername())
//                ->orderBy('tabla.id', 'desc')
//                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//                ->getQuery();

            $qb = $entityManager->createQueryBuilder();
            $qb->select(array('i', 'mov.areaDestino', 'i.num_inventario', 'i.id', 'i.estadoMovimiento'))
                ->from('AppBundle\Entity\incidencia', 'i')
                ->leftJoin('AppBundle\Entity\movimientoI_AF', 'mov', \Doctrine\ORM\Query\Expr\Join::WITH, 'i = mov.incidencia')
                ->where("i.tipo =:tipo ")
                ->setParameter('tipo','Movimiento Activo Fijo')
                ->andWhere('i.user=:usuario_actual')
                ->setParameter('usuario_actual', $this->getUser()->getUsername())
         //       ->andWhere('i.estadoMovimiento=:estado')
                ->orderBy('i.id', 'DESC');

            $query = $qb->getQuery();
            $results = $query->getResult();
        }

        $products = $query->getResult();
        // dump($products);die();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
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

            $entityManager = $this->getDoctrine()->getManager();
            //$chasis = $entityManager->getRepository('AppBundle:cpuchasis')->findOneBy(['inventario'=>$inventarios[0]->getId()]);
            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }

        //    $dql = "SELECT e.numInventario,m as movimiento FROM AppBundle:equipo e INNER JOIN AppBundle:incidencia i INNER JOIN AppBundle:movimientoI_AF m WHERE i.idE = e.id and i.id=m.incidencia ORDER BY i.id";
        //$query2 = $entityManager->createQuery($dql);
        // $numeros = $query->execute();
      //// dump($products);die();
        return $this->render(
            'activos_fijos/historial_movimientos.html.twig', array('pagination' => $pagination, 'inventarios' => $inventarios, 'numeros' => '',
            'areas' => $area, 'lista_historial' => $products, 'estados' => $estados,'entity'=>$this->getUser()

        ));
    }

    /**
     * @Route("reportes/comprobar_movimiento_activos", name="comprobar_movimiento_activos")
     */
    public function comprobarMovimientosAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $lista = $request->query->get('lista');
   //  dump($lista);die();

        $estados = [];
        $cont = 0;
        foreach ($lista as $l) {
            $estado_movimiento = 'Pendiente';
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
            //    dump($l);die();
                if ($l['estadoMovimiento'] == "Pendiente") {
                    // dump($l);die();
                    $idCostoDestino = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $l['areaDestino']])[0]->getIdCosto();
                    $idCostoActual = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $l['dpto']])[0]->getIdCosto();
                    $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($l['id']);
                    $numeroInventarioActual = explode(' ', $l['num_inventario'])[0];
                    //$area = $entityManager->getRepository('AppBundle:area')->findBy(['id' => $idC->getId()])[0]->getIdArea();
                    // $idArea = explode(' ', $area)[0];
                    // dump(substr($area, 0,''));
                    // dump($numeroInventarioActual);die();
                    //comprobar $sql = "SELECT cc.Id_Ccosto FROM dbo.Activo_Fijo cc WHERE cc.Id_AreaResp='".$idArea."' and cc.Id_ActivoFijo='".$numeroInventarioActual."'";

//                if()

                    $sql = "SELECT cc.ID_AreaResp,cc.Id_Ccosto FROM dbo.Activo_Fijo cc WHERE  cc.Id_Rotulo='" . $numeroInventarioActual . "'";
                    $query = $coneccionAssets->query($sql);

                    $idcosto = 0;
                    $responseArray = array();

                    if ($query) {
                        //  $area = array();
                        $respuestasMovimiento = array();
                        while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                            $area = $var;
                            //dump("hola");die();
                            $idcostoDepAsset = $area['ID_AreaResp'];
                            $idAreaAsset = $area['Id_Ccosto'];
//                        dump($numeroInventarioActual);
//                        dump($area);
                        dump("Departamento Actual: " . $idCostoActual);
                        dump("Departamento Destino: " . $idCostoDestino);
                        dump("Departamento Actual en Assets: " . $idcostoDepAsset);
                        dump("Area Actual en Assets: " . $idAreaAsset);


                            if ($idCostoDestino == $idcostoDepAsset) {
                                $estado_movimiento = "Realizado";

                                $estados[$cont] = array(
                                    "estado" => $estado_movimiento
                                );
                            } else {
                                $estados[$cont] = array(
                                    "estado" => "Pendiente"
                                );
                            }
                            $incidencia->setEstadoMovimiento($estado_movimiento);
                            dump($estado_movimiento);
                        }

                    }
                    $entityManager->persist($incidencia);
                }
            } catch (\PDOException $e) {
                die("No se conecta con el servidor! - " . $e->getMessage());
            }
            $cont = $cont + 1;
            // $incidencia->setEstadoMovimiento($estado_movimiento);

        }

        dump($estados);
        die();
        $responseArray = array();
        foreach ($estados as $r) {
            $responseArray[] = array(
                "estadoMovimiento" => $r['estado']
            );
        }
        $entityManager->flush();
//        dump($responseArray);
//        die();

        // return new JsonResponse($responseArray);
        return $this->redirectToRoute('lista_incidencias_movimiento', ['estados' => $responseArray]);
    }


    /**
     * @Route("reportes/comprobar_movimiento_incidencia", name="comprobar_movimiento_incidencia")
     */
    public function comprobarMovimientosIncidenciasAction(Request $request)
    {
        $mov = 'Movimiento Activo Fijo';
        $mov2 = 'Edicion';
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:incidencia');
        if ($this->isGranted('ROLE_USER') or $this->isGranted('ROLE_JEFE_DEP') or $this->isGranted('ROLE_AFT')) {

            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:incidencia');
            $query = $repository->createQueryBuilder('i')
                ->where("i.user = usuario_actual")
                ->setParameter("usuario_actual",$this->getUser()->getUsername())
                ->andWhere("i.tipo !='$mov2'")
                ->andWhere("i.tipo !='$mov'")
                ->orderBy('i.fecha', 'desc')
//        ->distinct('!tabla.tipoEquipo', !'tabla.fecha', !'tabla.tipo=Edicion')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))

                ->getQuery();
            $products = $query->getResult();

        } else {
            $inventarios = $applicationRepository->findAll();
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:incidencia');
            $query = $repository->createQueryBuilder('i')
                ->select('i')
                ->where("i.tipo !='$mov'")
                ->andWhere("i.tipo !='$mov2'")
                ->orderBy('i.fecha', 'desc')
                ->distinct('!i.tipoEquipo', !'i.fecha', !'i.tipo=Edicion')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            // dump($query->execute());die();

            $products = $query->getResult();
            // dump($query);die();
        }
        $listaDatosMov = [];
        $cont = 0;
        foreach ($products as $p) {
            // if($p->getEstado()=='Traslado' or $p->getEstado()=='Reparacion'or $p->getEstado()=='Reparación'or $p->getEstado()=='Reparado'or $p->getEstado()=='Reposicion'){
            if ($p->getEstadoMovimiento() == "Pendiente") {
                $listaDatosMov[$cont]['numeroI'] = $p->getNumInventario();
                $listaDatosMov[$cont]['fecha'] = $p->getFecha();
                $listaDatosMov[$cont]['idIncidencia'] = $p->getId();
                $listaDatosMov[$cont]['tipoMovimiento'] = $p->getTipoMov();
                $listaDatosMov[$cont]['estadoActual'] = $p->getEstado();
            }

            $cont = $cont + 1;
        }

//       dump($request);die();
//        $entityManager = $this->getDoctrine()->getManager();
//        $lista = $request->query->get('lista');
       // dump($lista);
        $estado_movimiento = 'Pendiente';
        $estados = [];
        $cont = 0;
        $movimientoActual = '';
        foreach ($listaDatosMov as $l) {
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
                // dump($l['areaDestino']);
                $movimientoActual = '';
                $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($l['idIncidencia']);
                if (key_exists('tipoMovimiento', $l)) {
                    if ($l['tipoMovimiento'] == 'Envio a taller' or $l['tipoMovimiento'] == 'Reparado' or $l['tipoMovimiento'] == 'Reposicion' or $l['tipoMovimiento'] == 'Reparación' or $l['tipoMovimiento'] == 'Reparación PC' or $l['tipoMovimiento'] == 'Reparacion PC' or $l['tipoMovimiento'] == 'Reparacion' or $l['tipoMovimiento'] == 'Regreso de taller' and ($l['tipoMovimiento'] != 'Instalacion')) {
                        //$movimientoActual = $entityManager->getRepository('AppBundle:movimiento')->findBy(['incidencia' => $incidencia->getId()]);
                        $repository = $this->getDoctrine()
                            ->getRepository('AppBundle:movimiento');
                        $movimientoActual = $repository->createQueryBuilder('tabla')
                            ->where('tabla.incidencia = :inci')
                            // ->andWhere('tabla.inventario =: idE')
                            ->setParameter('inci', $incidencia->getId())
                            ->orderBy('tabla.id', 'DESC')
                            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                            ->getQuery()->getResult();


//                        dump("hola");
//
//                        dump($movimientoActual);
//                        dump($incidencia);
                    } else {
                        //  dump("aqui");
                        if ($l['tipoMovimiento'] != 'Instalacion') {
                            $movimientoActual = $entityManager->getRepository('AppBundle:movimientoI')->findBy(['incidencia' => $incidencia->getId()]);
                        }
                        // dump($movimientoActual);
                        // dump($incidencia);

                    }
                    if ($movimientoActual != '' and $movimientoActual != null) {

                        $areaD = explode('   ', $movimientoActual[0]->getAreaDestino())[0];

                       // dump($areaD);
                        if ($areaD == 'Taller TECUN') {
                            $idCD = 46;
                        } else {
//                            dump($entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $areaD])[0])->getIdCosto();
//                            die();
                            $idCD = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $areaD])[0]->getIdCosto();
                            // dump('hola' . $idCD);
                        }
                        $areaE = explode('   ', $movimientoActual[0]->getAreaEntrega())[0];

                        if ($areaE == 'Taller TECUN') {
                            $idCostoActual = 46;
                        } else {
                            // dump($movimientoActual[0]->getAreaEntrega());die();
                            $idCostoActual = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $areaE])[0]->getIdCosto();
                        }
                        $numeroInventarioActual = $incidencia->getNumInventario();
                        //$area = $entityManager->getRepository('AppBundle:area')->findBy(['id' => $idC->getId()])[0]->getIdArea();
                        // $idArea = explode(' ', $area)[0];
                        // dump(substr($area, 0,''));
                       // dump($numeroInventarioActual);
                        //comprobar $sql = "SELECT cc.Id_Ccosto FROM dbo.Activo_Fijo cc WHERE cc.Id_AreaResp='".$idArea."' and cc.Id_ActivoFijo='".$numeroInventarioActual."'";
                        $sql = "SELECT cc.ID_AreaResp,cc.Id_Ccosto FROM dbo.Activo_Fijo cc WHERE  cc.Id_Rotulo='" . $numeroInventarioActual . "'";
                        //  $sql = "SELECT cc.ID_AreaResp,cc.Id_Ccosto FROM dbo.Activo_Fijo cc WHERE  cc.Id_Rotulo='025084'";
                        $query = $coneccionAssets->query($sql);

                        $idcosto = 0;
                        $responseArray = array();

                        if ($query) {
                            //  $area = array();
                            $respuestasMovimiento = array();

                            while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                                $area = $var;
                             //   dump($area);
                                $idcostoDepAsset = $area['ID_AreaResp'];
                                //     dump($area);
                                $idAreaAsset = $area['Id_Ccosto'];
//                                dump("Departamento Actual: " . $idCostoActual);
//                                dump("Departamento Destino: " . $idCD);
//                                dump("Departamento Actual en Assets: " . $idcostoDepAsset);
//                                dump("Area Actual en Assets: " . $idAreaAsset);
//                                dump($idCD);

                                if ($idCD == $idcostoDepAsset) {
                                    // dump("Iguales");
                                    $estado_movimiento = "Realizado";

//                                    $estados[$cont] = array(
//                                        "estado" => $estado_movimiento
//                                    );
                                } // dump("Movimiento realizado");
                                else {
                                    $estado_movimiento = "Pendiente";
                                }
                              //  dump($estado_movimiento);
                                $incidencia->setEstadoMovimiento($estado_movimiento);
                                $entityManager->persist($incidencia);
                                // dump("Contador: ".$cont);

                            }


                        }

                    }
                }

                //dump(substr($idArea, 0,''));
                //  dump($idArea);die();

//                   dump($idCD);die();

                // $idCostoDestino = $entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimientoActual[0]->getAreaDestino()])[0]->getIdCosto();

                // die();
                // dump($entityManager->getRepository('AppBundle:departamento')->findBy(['name' => $movimientoActual[0]->getAreaEntrega()]));die();

            } catch
            (\PDOException $e) {
                die("No se conecta con el servidor! - " . $e->getMessage());
            }
            $cont = $cont + 1;
            //  dump($estado_movimiento);die();

        }

//        dump($l);
//        die();

//        $responseArray = array();
//        foreach ($estados as $r) {
//            $responseArray[] = array(
//                "estadoMovimiento" => $r['estado']
//                "estadoMovimiento" => $r['estado']
//            );
//        }
//        dump($responseArray);
//        die();

        // return new JsonResponse($responseArray);

        $entityManager->flush();
        return $this->redirectToRoute('lista_inci');
    }


    /**
     * @Route("reportes/nombreArea_Activo", name="nombreArea_Activo")
     */
    public function nombreArea_ActivoAction(Request $request, $idArea, $dep, $centros, $lista)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto' => $idArea])[0];
        //dump($applicationRepository-);die();

        //  die();
        return $this->render(
            'activos_fijos/lista_activos_fijos.html.twig',
            [
                'name' => $applicationRepository,
                'dep' => $dep,
                'centros' => $centros,
                'lista' => ''

            ]
        );

    }

    /**
     * @Route("/reportes/activos_fijos/eliminar_movimiento/{id}", name="remove_movimientoAFT")
     */
    public
    function removeEstacionAction($id)
    {
        $incidencia = $this->getDoctrine()->getRepository('AppBundle:incidencia')->find($id);
        $movimiento = $this->getDoctrine()->getRepository('AppBundle:movimientoI_AF')->findBy(['incidencia' => $incidencia->getId()]);

//        dump($incidencia);dump($movimiento);
//        die();

        if (!$incidencia) {
            throw $this->createNotFoundException(
                'No se ha encontrado ningun inventario con este ' . $id
            );
        }

        $em = $this->getDoctrine()->getManager();
        if ($movimiento != []) {
            if (sizeof($movimiento) > 1) {
                foreach ($movimiento as $l) {
                    $em->remove($l);
                }
            } else {
//        dump($movimiento);
//        die();
                $em->remove($movimiento[0]);
            }
        }
        $em->remove($incidencia);
        $em->flush();

        // return new Response("El plan con id {$id} ha sido eliminado");
        $this->addFlash('success', 'El movimiento con id :' . $id . ' ha sido eliminado');
        return $this->redirectToRoute('lista_incidencias_movimiento');
    }

    /**
     * @Route("/reportes/activos_fijos/filterCentroCostos/{dep}/{sort}/{direction}",name="centroCosto_filter")
     * @param Request $request
     * @param $usuarios
     * @return Response
     */
    public
    function filterCentroCostoAction(Request $request, $dep, $usuarios = 0, $sort, $direction)
    {
        $ordenar = $sort;
        $direccion = $direction;
        if ($request->request->get('sort')) {
            $ordenar = $request->request->get('sort');
//            dump($direction);
//            die();
        }
        if ($request->request->get('direction')) {
            $direccion = $request->request->get('direction');

        }
        if ($request->request->get('usuarios')) {
            $nombre_departamento = $request->request->get('usuarios')[0];

            //  dump('aquii');
        } else {
            $nombre_departamento = $request->query->get('usuarios');
            //   dump('noo');
        }
//        dump($ordenar); dump($direccion);
//      die();
//        $usuarios=new  ArrayCollection();
//        $usuarios[0]=$nombre_departamento;

        $entityManager = $this->getDoctrine()->getManager();

        $em = $this->get('doctrine.orm.entity_manager');
        // $idArea = $em->getRepository('AppBundle:departamento')->findBy(['name' => $nombre_departamento])[0]->getArea()->getIdArea();
        $centrosC = $em->getRepository('AppBundle:departamento')->findBy(['name' => $nombre_departamento]);
        $idCostoc = $centrosC[0]->getIdCosto();
        $area = $em->getRepository('AppBundle:area')->findBy(['id' => $centrosC[0]->getArea()->getId()]);
//       dump($centrosC);
//        die();
//        $equipoRepository = $em->getRepository("AppBundle:equipoAssets");
////        $query = $equipoRepository->createQueryBuilder("q")
////            ->where("q.id_area = :id")
////            ->setParameter("id", $idCostoc)
////            ->andWhere("q.activo= :act")
////            ->setParameter("act", 1)
////            ->getQuery();
//        $query = $equipoRepository->createQueryBuilder("q")
//            ->where("idCostoActual =:id")
//            ->setParameter("idCostoActual", substr('q'.'id_area' ,-3))
//            ->setParameter("id", $idCostoc)
//            ->andWhere("q.activo= :act")
//            ->setParameter("act", 1)
//            ->getQuery();
//
//
//        dump($dql);
//       dump($query->execute());
//        die();
        $qb = $em->createQueryBuilder();
        $qb->select('t');
        $qb->from('AppBundle:equipoAssets', 't');
        $qb->where(
            $qb->expr()->eq($qb->expr()->substring('t.id_costo', -3, 3), $idCostoc));
        $qb->andWhere('t.activo=:act');
        $qb->setParameter('act', 1);
        $qb->andWhere('t.id_area=:area');
        $qb->setParameter('area', $area[0]->getNombre() . $area[0]->getIdArea());
        $qb->orderBy($sort, $direccion);
//        dump($area);
//        dump($area[0]->getIdArea());
//        dump($qb->getQuery()->getResult());
//        die();
        $equiposA = $em->getRepository('AppBundle:equipoAssets')->findAll();
        $lista = new ArrayCollection();
        foreach ($equiposA as $e) {
            $idCostoE = substr($e->getIdCosto(), -3);
            if ($idCostoE == $idCostoc) {
                $lista->add($e);
            }
//            // dump($idCostoE);die();
        }
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1)
        );
        $pagination->setParam('usuarios', $nombre_departamento);
        // $pagination->setParam('sort','descripcion');
        $resultados = $this->json($pagination);
//        $response = new Response($pagination->getItems());
//       $response->headers->set('Content-Type', 'application/json');

//        $l = serialize($qb->getQuery()->execute());
//        $lj=json_encode($lista);
//        $encoders = [new XmlEncoder(), new JsonEncoder()];
//        $normalizers = [new ObjectNormalizer()];
//
//        $serializer = new Serializer($normalizers, $encoders);
//        $jsonContent = $serializer->serialize($person, 'json');
//        dump($lista);
//     //  dump($this->json($pagination->getItems()));
//       dump($pagination);
//       die();
//        dump($pagination);
//        dump($lista);
//        die();
        // dump( $query->execute());die();
        return $this->render('activos_fijos/lista_activos_fijos.html.twig', [
            'lista' => $lista,
            'dep' => $dep,
            'orden' => $ordenar,
            'dir' => $direccion,
            'jsonList' => $resultados,
            'centros' => $centrosC,
            'pagination' => $pagination

        ]);

        $this->addFlash('alert', 'No existe equipo con los datos seleccionados');
        return $this->redirectToRoute('lista_activos_fijos', [usuarios => $nombre_departamento]);
    }

//    /**
//     * @Route("reportes/listadoActivosFijos",name="listadoA")
//     */
//    public function listadoActivos2Action(Request $request){
//        dump($request);die();
//    }


    /**
     * @Route("/reportes/utiles/filterCentroCostos/{dep}/{sort}/{direction}",name="centroCosto_filterUtiles")
     * @param Request $request
     * @param $usuarios
     * @return Response
     */
    public
    function filterCentroCostoUtilesAction(Request $request, $dep, $usuarios = 0, $sort=null, $direction=null)
    {
        $ordenar = $sort;
        $direccion = $direction;
       // dump($request);
     //  die();
        if ($request->request->get('usuarios')) {
            $nombre_departamento = $request->request->get('usuarios')[0];

            //  dump('aquii');
        } else {
            $nombre_departamento = $request->query->get('usuarios');
            //   dump('noo');
        }
//      $ordenar = null;
//        $direccion = null;

        if ($request->request->get('sort')) {
            $ordenar = $request->request->get('sort')[0];
        }
        if ($request->request->get('direction')) {
            $direccion = $request->request->get('direction')[0];
        }
            //  dump('aquii');

//        dump($request); dump($nombre_departamento);
//      die();
//        $usuarios=new  ArrayCollection();
//        $usuarios[0]=$nombre_departamento;

        $entityManager = $this->getDoctrine()->getManager();

        $em = $this->get('doctrine.orm.entity_manager');
        // $idArea = $em->getRepository('AppBundle:departamento')->findBy(['name' => $nombre_departamento])[0]->getArea()->getIdArea();
        $centrosC = $em->getRepository('AppBundle:departamento')->findBy(['name' => $nombre_departamento]);
        //dump($centrosC);die();
        $idCostoc = $centrosC[0]->getIdCosto();
        $area = $em->getRepository('AppBundle:area')->findBy(['id' => $centrosC[0]->getArea()->getId()]);
//       dump($centrosC);
//        die();
//        $equipoRepository = $em->getRepository("AppBundle:equipoAssets");
////        $query = $equipoRepository->createQueryBuilder("q")
////            ->where("q.id_area = :id")
////            ->setParameter("id", $idCostoc)
////            ->andWhere("q.activo= :act")
////            ->setParameter("act", 1)
////            ->getQuery();
//        $query = $equipoRepository->createQueryBuilder("q")
//            ->where("idCostoActual =:id")
//            ->setParameter("idCostoActual", substr('q'.'id_area' ,-3))
//            ->setParameter("id", $idCostoc)
//            ->andWhere("q.activo= :act")
//            ->setParameter("act", 1)
//            ->getQuery();
//
//
//        dump($dql);
//        dump($request);
//        die();

//        $dql = "SELECT t.id,u.idUH,u.descripcion,t.cantidad,t.nombreCosto,t.nombreArea,t.codigoCosto,t.codigoArea FROM AppBundle:utilDetalles t INNER JOIN AppBundle:util u WHERE t.util = u AND ORDER BY t.id ";
//        $query = $entityManager->createQuery($dql);
//        if ($request->request->get('sort')) {
//            $ordenar = $request->request->get('sort');
////            dump($direction);
////            die();
//            if ($request->request->get('direction')) {
//                $direccion = $request->request->get('direction');
//
//            }

        if($ordenar!=null){
            $qb = $em->createQueryBuilder();
            $qb->select('t.id', 'u.idUH', 'u.descripcion', 't.cantidad', 't.nombreCosto', 't.nombreArea', 't.codigoCosto', 't.codigoArea');
            $qb->from('AppBundle:utilDetalles', 't');
            $qb->innerJoin('AppBundle:util', 'u');
            $qb->where('t.util=u.id');
            $qb->andWhere('t.codigoCosto=:codigoC');
            $qb->setParameter('codigoC', $area[0]->getIdArea());
            $qb->andWhere('t.codigoArea=:area');
            $qb->setParameter('area', $idCostoc);
            $qb->orderBy($ordenar,$direccion);
        }else{
            $qb = $em->createQueryBuilder();
            $qb->select('t.id', 'u.idUH', 'u.descripcion', 't.cantidad', 't.nombreCosto', 't.nombreArea', 't.codigoCosto', 't.codigoArea');
            $qb->from('AppBundle:utilDetalles', 't');
            $qb->innerJoin('AppBundle:util', 'u');
            $qb->where('t.util=u.id');
            $qb->andWhere('t.codigoCosto=:codigoC');
            $qb->setParameter('codigoC', $area[0]->getIdArea());
            $qb->andWhere('t.codigoArea=:area');
            $qb->setParameter('area', $idCostoc);
        }

           // $qb->add('Order By',$ordenar, $direccion);

//            dump($request);
//            dump($ordenar);dump($direccion);
//        //  dump($qb->getQuery());
//          die();
//        }else{
//            $qb = $em->createQueryBuilder();
//            $qb->select('t.id', 'u.idUH', 'u.descripcion', 't.cantidad', 't.nombreCosto', 't.nombreArea', 't.codigoCosto', 't.codigoArea');
//            $qb->from('AppBundle:utilDetalles', 't');
//            $qb->innerJoin('AppBundle:util', 'u');
//            $qb->where('t.util=u.id');
//            $qb->andWhere('t.codigoCosto=:codigoC');
//            $qb->setParameter('codigoC', $area[0]->getIdArea());
//            $qb->andWhere('t.codigoArea=:area');
//            $qb->setParameter('area', $idCostoc);
//            dump($request);
//            dump($qb->getQuery());
//        }


        // $qb->orderBy($ordenar,$direccion);
//        dump($area);
//        dump($idCostoc);
////        dump($area[0]->getIdArea());
//        dump($qb->getQuery()->getResult());
//        die();
        if ($qb->getQuery()->execute() == []) {
            $this->addFlash('error', 'Este centro de costo no contiene utiles');
            return $this->redirectToRoute('lista_utiles');
        }
        $utiles = $em->getRepository('AppBundle:utilDetalles')->findAll();
        $lista = new ArrayCollection();
        foreach ($utiles as $e) {
            // $idCostoE = substr($e->getIdCosto(), -3);
            if ($e->getCodigoArea() == $idCostoc) {
                $lista->add($e);
            }
//            // dump($idCostoE);die();
        }
        $lista_aImprimir = [];
        $cont = 0;
        foreach ($lista as $l) {
            $lista_aImprimir[$cont]['codigo'] = $l->getUtil()->getIdUH();
            $lista_aImprimir[$cont]['descripcion'] = $l->getUtil()->getDescripcion();
            $lista_aImprimir[$cont]['area'] = $l->getNombreArea() . $l->getCodigoArea();
            $lista_aImprimir[$cont]['costo'] = $l->getNombreCosto() . $l->getCodigoCosto();
            $lista_aImprimir[$cont]['cantidad'] = $l->getCantidad();
            $cont = $cont + 1;
        }
        $listaImp = json_encode($lista_aImprimir);
        //   dump($qb->getQuery()->execute());die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1)

        );
        $pagination->setParam('usuarios', $nombre_departamento);
        // $pagination->setParam('sort','descripcion');

//        dump($pagination);
//        dump($qb->getQuery()->execute());
//        die();
        // dump( $query->execute());die();
        return $this->render('utiles/lista_utiles.html.twig', [
            'lista' => $qb->getQuery()->execute(),
            'listaI' => $listaImp,
            'dep' => $dep,
            'orden' => $ordenar,
            'dir' => $direccion,
            'centros' => $centrosC,
            'pagination' => $pagination

        ]);

        $this->addFlash('alert', 'No existe equipo con los datos seleccionados');
        return $this->redirectToRoute('lista_utiles', [usuarios => $nombre_departamento]);
    }

}
