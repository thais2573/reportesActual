<?php
/**
 * Created by PhpStorm.
 * User: Thais
 * Date: 2/15/2019
 * Time: 12:58 PM
 */

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\area;
use AppBundle\Entity\componente;
use AppBundle\Entity\componente_de_inventarios;
use AppBundle\Entity\departamento;
use AppBundle\Entity\equipo;
use AppBundle\Entity\equipo_de_inventarios;
use AppBundle\Entity\equipoAssets;
use AppBundle\Entity\incidencia;
use AppBundle\Entity\inventario;
use AppBundle\Entity\inventarios_estacion;
use AppBundle\Entity\periferico;
use AppBundle\Entity\ram;
use AppBundle\Entity\scanner;
use AppBundle\Entity\taller;
use AppBundle\Entity\tarjeta_video;
use AppBundle\Entity\teclado;
use AppBundle\Entity\temporal;
use AppBundle\Form\chasisFormType;
use AppBundle\Form\entregaTallerFormType;
use AppBundle\Form\equipoFomType;
use AppBundle\Form\Estacion2Form;
use AppBundle\Form\EstacionForm;
use AppBundle\Form\perifericoType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Tests\Fixtures\Type;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class EstacionController extends Controller
{
    private $comp = '';
    private $filters = [];
    private $pagination = [];
    private $componente_seleccionado = '';

    private $tipo = '';

    public $se_guardo = false;
    public $idactual = '';
    public $nombreE = '';
    public $idE = '';

    private $incidencias = '';

    public $lista_componentes = [];
    public $coleccion = '';
    public $lista_temporal = null;

    public function __construct()
    {
        $this->lista_componentes = new ArrayCollection();
        $this->coleccion = new ArrayCollection();

    }

    /**
     * @Route("reportes/estacion/adicionar_componentes/{nombre_estacion}/{idestacion}", name="adicionar_componentes")
     */
    public function adicionarComponentesAdminAction($nombre_estacion, $idestacion)
    {


        return $this->render(
            'estacion_trabajo/adicionar_componentes.html.twig',
            [

                'nombre_estacion' => $nombre_estacion,
                'idestacion' => $idestacion,
                'tipo' => $this->tipo,
                'lista_componentes' => $this->lista_componentes,
                'filters' => $this->filters,
                'pagination' => $this->pagination
            ]
        );
    }

    /**
     * @Route("reportes/estacion/adicionar_componentesNI2/{nombre_estacion}/{idestacion}/{id_incidencia}", name="adicionar_componentes_NI")
     */
    public function adicionarComponentesNI2Action($nombre_estacion, $idestacion, $id_incidencia)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($id_incidencia);
        $estacion = $entityManager->getRepository('AppBundle:inventario')->find($idestacion);

//    foreach ($estacion->getInventarios() as $inventarios){
//      dump($inventarios->getEstado());
//    }
//    die();
        //  dump($inventario_actual);die();
        $equipos = $estacion->getEquipos();
        // dump(sizeof($estacion->getInventarios()));die();
        return $this->render(
            'estacion_trabajo/adicionar_componentesNI.html.twig',
            [
                'lista_equipos' => $equipos,
                'nombre_estacion' => $nombre_estacion,
                'idestacion' => $idestacion,
                'tipo' => $this->tipo,
                'lista_componentes' => $this->lista_componentes,
                'filters' => $this->filters,
                'pagination' => $this->pagination,
                'incidencia' => $incidencia,
            ]
        );
    }

    /**
     * @Route("/incidencia/instalacion_equipos/{id}/",name="instalacion_equipos")
     */
    public function instalarEquiposEnEstacionAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($id);
        $cantE = $entityManager->getRepository('AppBundle:equipo')->CantidadEquipos($inventario->getId());
        $cantM = 0;
        $cantBackup = 0;
        $cantImpresora = 0;
        $cantEst = 0;
        $cantScan = 0;
        $cantChasis = 0;
        foreach ($cantE as $cant) {
            if ($cant['tipoEquipo'] == 'monitor') {
                $cantM = $cant[1];
            } elseif ($cant['tipoEquipo'] == 'backup') {
                $cantBackup = $cant[1];
            } elseif ($cant['tipoEquipo'] == 'scanner') {
                $cantScan = $cant[1];
            } elseif ($cant['tipoEquipo'] == 'estabilizador') {
                $cantEst = $cant[1];
            } elseif ($cant['tipoEquipo'] == 'impresora') {
                $cantImpresora = $cant[1];
            } elseif ($cant['tipoEquipo'] == 'cpuchasis') {
                $cantChasis = $cant[1];
            }
        }

        return $this->render('estacion_trabajo/instalacion_equipos.html.twig', ['estacion' => $inventario, 'cantM' => $cantM, 'cantI' => $cantImpresora
            , 'cantB' => $cantBackup, 'cantE' => $cantEst, 'cantS' => $cantScan, 'cantC' => $cantChasis
        ]);
    }


    /**
     * @Route("reportes/estacion/refrescar/{nombre_estacion}/{tipo}/{incidencia_id}", name="refrescar_lista")
     */
    public function refrescarAction($nombre_estacion, $tipo, $incidencia_id)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository3 = $entityManager->getRepository('AppBundle:temporal');
        $lista = $applicationRepository3->findAll();

        $applicationRepository3 = $entityManager->getRepository('AppBundle:inventario');
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($incidencia_id);
        $idE = $applicationRepository3->findBy(['nombreRed' => $nombre_estacion])[0]->getId();
        $this->lista_componentes = $lista;
//     dump($tipo);
//     die();
        if ($tipo == 'nuevo') {
            return $this->render(
                'estacion_trabajo/adicionar_componentes.html.twig',
                [
                    'nombre_estacion' => $nombre_estacion,
                    'idestacion' => $idE,
                    'lista' => $this->lista_componentes,
                    'incidencia' => $incidencia
                ]
            );
        } else {
            $equipos = $entityManager->getRepository('AppBundle:equipo')->findBy(['estacion' => $applicationRepository3->find($idE)]);
            return $this->render(
                'estacion_trabajo/adicionar_componentesNI.html.twig',
                [
                    'nombre_estacion' => $nombre_estacion,
                    'idestacion' => $idE,
                    'lista' => $this->lista_componentes,
                    'incidencia' => $incidencia,
                    'lista_equipos' => $equipos
                ]
            );
        }

    }


    /**
     * @Route("reportes/lista_estaciones", name="lista_estaciones")
     */
    public function listaEstacionesAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:inventario');

        //$buscarAreaForm = $this->createForm('AppBundle\Form\buscarDepartamentoyArea');
        //  ,$private_message, array('user' => $this->getUser())
        //$buscarAreaForm->handleRequest($request);

        /* if ($buscarAreaForm->isSubmitted() && $buscarAreaForm->isValid()) {
       // $form->getData() holds the submitted values
       // but, the original `$task` variable has also been updated
       $plan = $buscarAreaForm->getData();


       $this->addFlash('success', 'Los datos han sido insertados correctamente');
       return $this->redirectToRoute('incidencia_filter');
     }*/
        //  $inventarios = $applicationRepository->findAll();

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:inventario');
        $inventarios = $repository->createQueryBuilder('tabla')
            //->where('tabla.estado = :inv')
            // ->andWhere('tabla.inventario =: idE')
            //->setParameter('inv', 'Activo')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery()->getResult();


        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
//        $paginator = $this->get('knp_paginator');
//        $pagination = $paginator->paginate(
//            $inventarios,
//            $request->query->getInt('page', 1),
//            10
//        );

        $estacionesVacias = new ArrayCollection();
        $componentesBasicos = ['cpuchasis', 'monitor', 'backup'];
        foreach ($inventarios as $i) {
            $contChasis = 0;
//            $contBackup=0;
//            $contMonitor=0;
            foreach ($i->getEquipos() as $equipo) {
                // dump($equipo);
                if ($equipo->getTipoEquipo() == $componentesBasicos[0]) {
                    $contChasis = $contChasis + 1;
                }
//                if($equipo->getTipoEquipo()==$componentesBasicos[1]){
//                    $contMonitor=$contMonitor+1;
//                }   if($equipo->getTipoEquipo()==$componentesBasicos[2]){
//                    $contBackup=$contBackup+1;
//                }
            }
            if ($contChasis == 0) {
                $estacionesVacias->add($i);
                //   $estacionesVacias->add('chasis');
            }
//           if ($contBackup==0){
//                $estacionesVacias->add($i);
//                $estacionesVacias->add('backup');
//            }
//            if($contMonitor==0){
//                $estacionesVacias->add($i);
//                $estacionesVacias->add('monitor');
//            }
            // die();
        }

        //  dump($estacionesVacias);die();


        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        return $this->render(
            'estacion_trabajo/lista_estaciones.html.twig', array( 'inventarios' => $inventarios,
            "filters" => $this->filters, 'areas' => $areas, 'estacionesSinChasis' => $estacionesVacias

        ));
    }


    /**
     * @Route("reportes/cargar_areas", name="cargar_areas")
     */
    public function listaAreas1Action(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:area');
        $lista_dep = $entityManager->getRepository('AppBundle:departamento')->findAll();

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
            // $idcosto = 0;
            //$departamento='';
            if ($query) {
                $area = array();
                $entity = new area();
                $em = $this->getDoctrine()->getManager();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;

                }

                foreach ($area as $a) {
                    $entity = new area();
                    $idcosto = $a['Id_Ccosto'];
                    $departamento = $a['Desc_Ccosto'];

                    set_time_limit(0);
                    $entity->setIdArea($idcosto);
                    $entity->setNombre($departamento);


                    /* if(mssql_num_rows($entity->)>0){
             break;
           }*/
                    $entityManager = $this->getDoctrine()->getManager();
                    $equipoE = $entityManager->getRepository('AppBundle:area')->findOneBy(['id_area' => $idcosto]);

                    if ($equipoE == null) {
                        $em->persist($entity);
                        $em->flush();
                    }


                    //  dump($departamento);die();

                }


            }
            //  $cant=0;

            //   $cant=$cant+1;
            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }
        $lista_areas = $applicationRepository->findAll();
        // dump($result[0]['Desc_Ccosto']);die();


        return $this->render(
            'tipo/list_departamentos.html.twig',
            [
                'lista' => $lista_areas,
                'dep' => $lista_dep,
                //'inventarios' => $inventarios,
                "filters" => $this->filters,
                "pagination" => $this->pagination
            ]
        );
    }

    /**
     * @Route("reportes/cargar_departamentos", name="cargar_departamentos")
     */
    public function listaDepAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:area');
        $applicationRepository2 = $entityManager->getRepository('AppBundle:departamento');

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
            $sql = "SELECT cc.Id_Ccosto, cc.Id_AreaResponsabilidad,cc.Desc_AreaResponsabilidad FROM dbo.Areas_Responsabilidad cc";
            $query = $coneccionAssets->query($sql);
            // $idcosto = 0;
            //$departamento='';
            if ($query) {
                $dep = array();
                $entity = new departamento();
                $em = $this->getDoctrine()->getManager();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $dep[] = $var;

                }

                foreach ($dep as $a) {
                    $entity = new departamento();
                    $idcosto = $a['Id_Ccosto'];
                    $id_area = $a['Id_AreaResponsabilidad'];
                    $departamento = $a['Desc_AreaResponsabilidad'];

                    set_time_limit(0);
                    $area = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
                    $entity->setArea($area[0]);
                    $entity->setName($departamento);
                    $entity->setIdCosto($id_area);

                    $entityManager = $this->getDoctrine()->getManager();
                    $equipoE = $entityManager->getRepository('AppBundle:departamento')->findOneBy(['idCosto' => $id_area]);

                    if ($equipoE == null) {
                        $em->persist($entity);

                    }

                }
                $em->flush();

            }
            //  $cant=0;

            //   $cant=$cant+1;
            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }
        $lista_areas = $applicationRepository->findAll();
        $lista_dep = $applicationRepository2->findAll();
        // dump($result[0]['Desc_Ccosto']);die();


        return $this->render(
            'tipo/list_departamentos.html.twig',
            [
                'lista' => $lista_areas,
                'dep' => $lista_dep,
                //'inventarios' => $inventarios,
                "filters" => $this->filters,
                "pagination" => $this->pagination
            ]
        );
    }


    /**
     * @Route("reportes/cargar_equipos", name="cargar_equipos")
     */
    public function listaEquiposAssetsAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $equipoA = $entityManager->getRepository('AppBundle:equipoAssets');

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
            $sql = "SELECT af.Desc_ActivoFijo, af.Id_Rotulo,af.Id_Ccosto,af.ID_AreaResp,af.Activo,af.Numero_Serie FROM dbo.Activo_Fijo af WHERE af.Activo=1";
            $query = $coneccionAssets->query($sql);
            // $idcosto = 0;
            //$departamento='';
            if ($query) {
                $equipos = array();

                $em = $this->getDoctrine()->getManager();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $equipos[] = $var;

                }
//          dump($equipos);
//         die();
                foreach ($equipos as $e) {

                    $entity = new equipoAssets();
                    $descripcion = $e['Desc_ActivoFijo'];
                    $numI = $e['Id_Rotulo'];
                    $idcosto = $e['Id_Ccosto'];
                    $idarea = $e['ID_AreaResp'];
                    $activo = $e['Activo'];
                    $serie = $e['Numero_Serie'];

                    // dump($idcosto);die();
                    set_time_limit(-1);

                    $costo = substr($idarea, 0, '3');
                    //dump($costo);die();

                    $area = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
                    $repository = $this->getDoctrine()
                        ->getRepository('AppBundle:departamento');
                    $departamento = $repository->createQueryBuilder('tabla2')
                        ->andWhere('tabla2.area=:area1')
                        ->setParameter('area1', $area[0]->getId())
                        ->andWhere('tabla2.idCosto=:idC')
                        ->setParameter('idC', $costo)
                        ->getQuery()->execute();
                    //    if($departamento!=[]){
//                    dump($area);
//                    dump($departamento);
//                    die();
                    $entity->setDescripcion($descripcion);
                    $entity->setNumInventario($numI);
                    if ($departamento) {
                        $entity->setIdCosto($departamento[0]->getName() . "" . $costo);
                        // dump($entity);
                        //  dump("holaa");die();
                    } else {
                        //    dump("no tengo dep");die();
                        $entity->setIdCosto($idcosto);
                    }
                    // $entity->setIdCosto($idcosto);
                    if ($area != []) {
                        $entity->setIdArea($area[0]->getNombre() . $idcosto);
                    } else {
                        $entity->setIdArea($idarea);
                    }
                    $entity->setActivo($activo);
                    $entity->setSerie($serie);
                    $entityManager = $this->getDoctrine()->getManager();
                    $equipoE = $entityManager->getRepository('AppBundle:equipoAssets')->findOneBy(['numInventario' => $numI]);
                    // dump($idcosto);
//                    dump($equipoE);
//                    dump($entity);
//                    die();
                    if ($equipoE == null) {
                        $em->persist($entity);
                        $em->flush();
                    } elseif ($equipoE->getNumInventario() == $entity->getNumInventario() and
                        ($equipoE->getIdArea() != $entity->getIdArea() or $equipoE->getIdCosto() != $entity->getIdCosto())
                    ) {
                        //   dump($idcosto);dump($idarea);
                        // $equipoE->setIdCosto($idcosto);
                        $costoD = null;
                        $areaD = null;
                        if ($area != []) {
                            $areaD = $area[0]->getNombre() . $idcosto;
                        } else {
                            $areaD = $idarea;
                        }
                        if ($departamento) {
                            $costoD = $departamento[0]->getName() . $costo;
                            // dump($entity);
                            //  dump("holaa");die();
                        } else {
                            //    dump("no tengo dep");die();
                            $costoD = $idcosto;
                        }
//                        $em->persist($entity);
                        $dql = "UPDATE AppBundle:equipoAssets e SET e.id_costo=' $costoD ',e.id_area='$areaD',e.activo=$activo WHERE e.numInventario=" . $numI . "";
                        // dump($dql);die();
                        $query = $em->createQuery($dql);
                        $a = $query->execute();

                    }

                    //  dump($equipoE);dump($entity);

                }
                // dump($idarea);
                // dump($area[0]);
                //  dump($entity);
                // dump($idcosto);
                //  dump($departamento);

//

                //  dump($entity);
//          die();
                /* if(mssql_num_rows($entity->)>0){
         break;
       }*/


                //  }
//                dump($entity);
//                die();

                $aset = $entityManager->getRepository('AppBundle:equipoAssets')->findAll();
//                dump($aset);
//                die();

                //  dump($departamento);die();


            }
            //  $cant=0;

            //   $cant=$cant+1;
            // dump($dep);die();
        } catch (\PDOException $e) {
            ("No se conecta con el servidor! - " . $e->getMessage());
        }
        $listaE = $equipoA->findAll();
        // dump($result[0]['Desc_Ccosto']);die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $aset,
            $request->query->getInt('page', 1)
        );
        return $this->render(
            'perifericos/equipos_asset.html.twig',
            [
                'lista' => $listaE,

                //'inventarios' => $inventarios,
                "filters" => $this->filters,
                "pagination" => $pagination
            ]
        );
    }

    /**
     * @Route("reportes/cargar_equiposAF/{dep}", name="cargar_equiposListaActivos")
     */
    public function listaEquiposActivosAssetsAction(Request $request, $dep)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $equipoA = $entityManager->getRepository('AppBundle:equipoAssets');
        $area = '';
//dump($request);die();
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
            $sql = "SELECT af.Desc_ActivoFijo, af.Id_Rotulo,af.Id_Ccosto,af.ID_AreaResp,af.Activo,af.Numero_Serie FROM dbo.Activo_Fijo af WHERE af.Activo=1";
            $query = $coneccionAssets->query($sql);
            // $idcosto = 0;
            //$departamento='';
            if ($query) {
                $equipos = array();

                $em = $this->getDoctrine()->getManager();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $equipos[] = $var;

                }
//          dump($equipos);
//         die();
                foreach ($equipos as $e) {

                    $entity = new equipoAssets();
                    $descripcion = $e['Desc_ActivoFijo'];
                    $numI = $e['Id_Rotulo'];
                    $idcosto = $e['Id_Ccosto'];
                    $idarea = $e['ID_AreaResp'];
                    $activo = $e['Activo'];
                    $serie = $e['Numero_Serie'];

                    // dump($idcosto);die();
                    set_time_limit(-1);

                    $costo = substr($idarea, 0, '3');
                    //dump($costo);die();

                    $area = $entityManager->getRepository('AppBundle:area')->findBy(['id_area' => $idcosto]);
                    $repository = $this->getDoctrine()
                        ->getRepository('AppBundle:departamento');
                    $departamento = $repository->createQueryBuilder('tabla2')
                        ->andWhere('tabla2.area=:area1')
                        ->setParameter('area1', $area[0]->getId())
                        ->andWhere('tabla2.idCosto=:idC')
                        ->setParameter('idC', $costo)
                        ->getQuery()->execute();
                    //    if($departamento!=[]){
//                    dump($area);
//                    dump($departamento);
//                    die();
                    $entity->setDescripcion($descripcion);
                    $entity->setNumInventario($numI);
                    if ($departamento) {
                        $entity->setIdCosto($departamento[0]->getName() . "" . $costo);
                        // dump($entity);
                        //  dump("holaa");die();
                    } else {
                        //    dump("no tengo dep");die();
                        $entity->setIdCosto($idcosto);
                    }
                    // $entity->setIdCosto($idcosto);
                    if ($area != []) {
                        $entity->setIdArea($area[0]->getNombre() . $idcosto);
                    } else {
                        $entity->setIdArea($idarea);
                    }
                    $entity->setActivo($activo);
                    $entity->setSerie($serie);
                    $entityManager = $this->getDoctrine()->getManager();
                    $equipoE = $entityManager->getRepository('AppBundle:equipoAssets')->findOneBy(['numInventario' => $numI]);
                    // dump($idcosto);
//                    dump($equipoE);
//                    dump($entity);
//                    die();
                    if ($equipoE == null) {
                        $em->persist($entity);
                        $em->flush();
                    } elseif ($equipoE->getNumInventario() == $entity->getNumInventario() and
                        ($equipoE->getIdArea() != $entity->getIdArea() or $equipoE->getIdCosto() != $entity->getIdCosto())
                    ) {
                        //   dump($idcosto);dump($idarea);
                        // $equipoE->setIdCosto($idcosto);
                        $costoD = null;
                        $areaD = null;
                        if ($area != []) {
                            $areaD = $area[0]->getNombre() . $idcosto;
                        } else {
                            $areaD = $idarea;
                        }
                        if ($departamento) {
                            $costoD = $departamento[0]->getName() . $costo;
                            // dump($entity);
                            //  dump("holaa");die();
                        } else {
                            //    dump("no tengo dep");die();
                            $costoD = $idcosto;
                        }
//                        $em->persist($entity);
                        $dql = "UPDATE AppBundle:equipoAssets e SET e.id_costo=' $costoD ',e.id_area='$areaD',e.activo=$activo WHERE e.numInventario=" . $numI . "";
                        // dump($dql);die();
                        $query = $em->createQuery($dql);
                        $a = $query->execute();

                    }

                    //  dump($equipoE);dump($entity);

                }


                // dump($area[0]);
                //  dump($entity);
                // dump($idcosto);
                //  dump($departamento);

//

                //  dump($entity);
//          die();
                /* if(mssql_num_rows($entity->)>0){
         break;
       }*/


                //  }
//                dump($entity);
//                die();


//                dump($aset);
//                die();

                //  dump($departamento);die();


            }
            //  $cant=0;

            //   $cant=$cant+1;
            // dump($dep);die();
        } catch (\PDOException $e) {
            ("No se conecta con el servidor! - " . $e->getMessage());
        }
        $aset = $entityManager->getRepository('AppBundle:equipoAssets')->findAll();
//        dump('hola');
//        die();
        //  $listaE = $equipoA->findAll();
        // dump($result[0]['Desc_Ccosto']);die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $aset,
            $request->query->getInt('page', 1)
        );
        // dump($pagination);die();
        return $this->render(
            'activos_fijos/lista_activos_fijos.html.twig',
            [
                'lista' => $aset,
                'dep' => $dep,
                'centros' => '',
                'area' => $area[0],
                //'inventarios' => $inventarios,
                "pagination" => $pagination
            ]
        );
    }

    /**
     * @Route("reportes/llenar_equipos", name="llenar_tablas_equipos")
     *
     */
    public function LlenarEquiposAssetsAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $equipoA = $entityManager->getRepository('AppBundle:equipoAssets');


        /**
         * Consulta para obtener los centros de costos
         */
        $backup = "INSERT INTO backup (serie, num_inventario, estado,modelo)
              SELECT serie,num_inventario,activo,descripcion
              FROM tb_equipoassets
              WHERE descripcion LIKE '%UPS%' or descripcion LIKE '%BACKUP%'";
        $query = $entityManager->getConnection()->prepare($backup);

        $monitor = "INSERT INTO monitor (serie, num_inventario, estado,modelo)
                   SELECT serie,num_inventario,activo,descripcion
                   FROM tb_equipoassets
                  WHERE descripcion LIKE '%MONITOR%' or descripcion LIKE '%DISPLAY%'";
        $query2 = $entityManager->getConnection()->prepare($monitor);

        $impresoras = "INSERT INTO impresora (serie, num_inventario, estado,modelo)
                    SELECT serie,num_inventario,activo,descripcion
                    FROM tb_equipoassets
                    WHERE descripcion LIKE '%IMPRESORA%' or descripcion LIKE '%EPSON%' or descripcion LIKE '%IMP%' or descripcion LIKE '%HP%' ";
        $query3 = $entityManager->getConnection()->prepare($impresoras);

        $estabilizador = "INSERT INTO estabilizador (serie, num_inventario, estado,modelo)
                   SELECT serie,num_inventario,activo,descripcion
                   FROM tb_equipoassets
                   WHERE descripcion LIKE '%ESTABILIZADOR%'";
        $query4 = $entityManager->getConnection()->prepare($estabilizador);

        $laptop = "INSERT INTO laptop (serie, num_inventario, estado,modelo)
               SELECT serie,num_inventario,activo,descripcion
               FROM tb_equipoassets
               WHERE descripcion LIKE '%LAPTOP%' or descripcion LIKE '%Laptop%'";
        $query5 = $entityManager->getConnection()->prepare($laptop);

        $escaner = "INSERT INTO scanner (serie, num_inventario, estado,modelo)
               SELECT serie,num_inventario,activo,descripcion
               FROM tb_equipoassets
               WHERE descripcion LIKE '%SCANNER%' or descripcion LIKE '%ESCANER%'";
        $query6 = $entityManager->getConnection()->prepare($escaner);

        $query->execute();
        $query2->execute();
        $query3->execute();
        $query4->execute();
        $query5->execute();
        $query6->execute();
        // $idcosto = 0;
        //$departamento='';
        /* if ($query) {
       $equipos = array();

       $em = $this->getDoctrine()->getManager();
       while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
         $equipos[] = $var;

       }
       //  dump($equipos);
       // die();
       foreach ($equipos as $e) {
         $entity = new equipoAssets();
         $descripcion = $e['Desc_ActivoFijo'];
         $numI = $e['Id_Rotulo'];
         $idcosto = $e['Id_Ccosto'];
         $id_area = $e['ID_AreaResp'];
         $activo = $e['Activo'];
         $serie = $e['Numero_Serie'];

         // dump($idcosto);die();

         set_time_limit(0);
         $entity->setDescripcion($descripcion);
         $entity->setNumInventario($numI);
         $entity->setIdCosto($idcosto);
         $entity->setIdArea($id_area);
         $entity->setActivo($activo);
         $entity->setSerie($serie);

         /* if(mssql_num_rows($entity->)>0){
            break;
          }
         $entityManager = $this->getDoctrine()->getManager();
         $equipoE = $entityManager->getRepository('AppBundle:equipoAssets')->findOneBy(['numInventario' => $numI]);

         if ($equipoE == null) {
           $em->persist($entity);
           $em->flush();
         }


       }
       $aset = $entityManager->getRepository('AppBundle:equipoAssets')->findAll();
       // dump($aset);die();

       //  dump($departamento);die();


     }
     //  $cant=0;

     //   $cant=$cant+1;
     // dump($dep);die();
   } catch (\PDOException $e) {
     ("No se conecta con el servidor! - " . $e->getMessage());
   }*/
        return $this->redirectToRoute('lista_equipos_assets');
    }


    /**
     * Matches /blog exactly
     * @Route("reportes/estacion/nueva_estacion", name="nuevo_inventario")
     */
    public function nuevoInventarioAction(Request $request, $nuevo = null,ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $nuevaEstacion = new inventario();
        //  $componentes=new ArrayCollection();
//        $comp=new componente();
//        $comp->setEstacion2($nuevaEstacion);

//        $comp->setModelo('Thais');
//        $comp->setEstado('Inactivo');
        //  $componentes->add($comp);

        // $nuevaEstacion->getComponente()->add($comp);
        //   $entityManager->persist($nuevaEstacion);
        $estacionForm = $this->createForm(EstacionForm::class);
        $estacionForm->handleRequest($request);

        $validator = $this->get('validator');


        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        $numI = $request->request->get('numI');

        //   $estacionForm->handleRequest($request);
        if ($estacionForm->isSubmitted() && $estacionForm->isValid()) {
           // $errors = $validator->validate($estacionForm);
            $estacion = $estacionForm->getData();
            if($request->get('costos')){
                $centroCosto=$entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto'=>$request->get('costos')])[0];
                // dump($centroCosto);die();
                $idCosto = $centroCosto->getId();
                $dep = $entityManager->getRepository('AppBundle\Entity\departamento')->findBy(['id' => $idCosto]);
                $estacion->setCentroCosto($dep[0]);
            }


            $errors = $validator->validate($estacion);
//            dump($request);
//            dump($estacion);die();
            if (count($errors) > 0) {
                $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
//                $areas = $entityManager->getRepository('AppBundle:area')->findAll();
                return $this->render('estacion_trabajo/new_estacion.html.twig', array(
                    'areas' => $areas,
                     'errores'=>$errors,
                    //   'estacion'=>$estacion,
                    'form' => $estacionForm->createView(),
                    'idestacion' => $this->idactual,
                    'nombre_estacion' => $this->nombreE,
                    'usuarios' => $usuarios
                ));
            }

            $equiposNuevos = [];
            $estacion->setActivo('Activo');
            $em = $this->getDoctrine()->getManager();
            $centroCosto=$entityManager->getRepository('AppBundle:departamento')->findBy(['idCosto'=>$request->get('costos')])[0];
           // dump($centroCosto);die();
            $idCosto = $centroCosto->getId();
            $dep = $entityManager->getRepository('AppBundle\Entity\departamento')->findBy(['id' => $idCosto]);
             //dump($estacionForm->getErrors());die();
            if ($request->request->get('equipos')) {
                $equiposNuevos = $request->request->get('equipos');
                foreach ($equiposNuevos as $eN) {
                    $equipo = $em->getRepository('AppBundle:equipo')->findBy(['numInventario' => $eN]);
                    $estacion->getEquipos()->add($equipo[0]);
                    $estacion->setCentroCosto($dep[0]);
                    $equipo[0]->setDepartamento($dep[0]);
                    $equipo[0]->setEstacion($estacion);
                    $equipo[0]->setEstado('Activo');
                }
            }
            foreach ($estacion->getComponente() as $c) {
                $c->setEstacion2($estacion);
                $entityManager->persist($c);
              //  $estacion->getComponente()->add($c);
            }


//          if(stristr($request->request,'equipo_')===False){
//            echo '"equipo_" no encontrado';
//
//            }
            //    dump($equiposNuevos);
//            dump($estacion);
//            dump($estacion->getComponente());
//            dump($estacion->getEquipos());
//            die();
//            dump($estacion);die();
            $estacion->setEstado('Activo');
            $entityManager->persist($estacion);
            $entityManager->flush();

            return $this->redirectToRoute("datos_estacion", ['id' => $estacion->getId()]);
        }
        //   } else
//        $new = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);
//        $dato = '';
//        if ($new != null) {
//            $dato = $new[0];
//        } else {
//            $dato;
//        }
//        dump($new);
//        die();
        $usuarios = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $areas = $entityManager->getRepository('AppBundle:area')->findAll();
        return $this->render('estacion_trabajo/new_estacion.html.twig', array(
            'areas' => $areas,
            //   'estacion'=>$estacion,
            'form' => $estacionForm->createView(),
            'idestacion' => $this->idactual,
            'nombre_estacion' => $this->nombreE,
            'usuarios' => $usuarios,
           'errores'=>'',
        ));

    }


    /**
     * Matches /blog exactly
     * @Route("reportes/estacion/nueva_estacion2", name="nuevaestacion")
     */
    public function nuevaEstAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $estacionForm = $this->createForm('AppBundle\Form\EstacionForm');
        $estacionForm->handleRequest($request);

        /*    $estacionForm->setData(
          array(
            'chasis' => array(
              array('options' => array(), 'char_desc' => 1),
              array('options' => array(), 'char_desc' => 2),
            ),
            'data_class'=>cpuchasis::class
          )
        );*/

        //$buscar = $this->createForm('AppBundle\Form\buscarperifericoFormType');


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
            $sql = "SELECT cc.Id_Ccosto, cc.Desc_Ccosto FROM dbo.Centro_Costo cc ";
            $query = $coneccionAssets->query($sql);
            $idcosto = 0;
            if ($query) {
                $area = array();

                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $area[] = $var;
                    $idcosto = $area[0]['Id_Ccosto'];
                    //dump($area[0]['Id_Ccosto']);die();
                }
            }


            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }


        if ($estacionForm->isSubmitted() && $estacionForm->isValid()) {


            $estacion = $estacionForm->getData();


            $idcosto = $request->get('d');
            //dump($idcosto[0]);die();
            $entityManager = $this->getDoctrine()->getManager();
            $estacion->setEstado('Activo');

            $estacion->setDpto($request->get('usuarios')[0]);
            $estacion->setChasis(null);
            $estacion->setTecnico($this->getUser());
            $estacion->setCentroCosto($idcosto[0]);


            $entityManager->persist($estacion);
            $entityManager->flush();

            // $entityManager->refresh($estacion);
            $this->nombreE = $estacion->getNombreRed();
            $this->idactual = $estacion->getID();

            // $this->addFlash('success', 'Inventario Creado Correctamente con id: '.$this->idactual);
            //,array('inventario'=>$ultimo_id)
            //dump($this->idactual);
            //  die();
            // dump($this->nombreE);
            // die();
            //dump($request);
            //die();
            return $this->redirectToRoute('adicionarChasisE', ['nombre_estacion' => $this->nombreE, 'idestacion' => $this->idactual]);
            /* return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
         //'estacionForm' => $estacionForm->createView(),
         'id'=>$this->idactual

       ));*/

        } else

            return $this->render('estacion_trabajo/nueva_estacion.html.twig', array(
                'areas' => $area,
                'estacionForm' => $estacionForm->createView(),
                'idestacion' => $this->idactual,
                'nombre_estacion' => $this->nombreE,
            ));

    }


    /**
     * Matches /blog exactly
     * @Route("reportes/estacion/new", name="nueva_estacion")
     */
    public function nuevaEstacionAction(Request $request)
    {
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


        $entityManager = $this->getDoctrine()->getManager();


        $em = $this->getDoctrine()->getManager();


        $monitor = new monitor();
        $estabilizador = new estabilizador();
        $scanner = new scanner();
        $impresora = new impresora();
        $backup = new backup();
        $estacion = new inventario();


        $bocinas = new bocinas();
        $fuente = new fuente();
        $hdd = new hdd();
        $lector = new lector();
        $micro = new microprocesador();
        $board = new motherboard();
        $mouse = new mouse();
        $ram = new ram();
        $tVideo = new tarjeta_video();
        $teclado = new teclado();
        $equipo = new cpuchasis();

        $equipo->addBocina($bocinas);
        $equipo->addFuente($fuente);
        $equipo->addHdd($hdd);
        $equipo->addLector($lector);
        $equipo->addMicro($micro);
        $equipo->addMouse($mouse);
        $equipo->addBoard($board);
        $equipo->addRam($ram);

        $equipo->addTv($tVideo);
        $equipo->addTeclado($teclado);

        $estacion->getMonitor()->add($monitor);
        $estacion->getEstabilizador()->add($estabilizador);
        $estacion->getScanner()->add($scanner);
        $estacion->getImpresora()->add($impresora);
        $estacion->getBackup()->add($backup);
        $estacion->getChasis()->add($equipo);

        $estacionForm = $this->createForm(Estacion2Form::class, $estacion);


        $estacionForm->handleRequest($request);
        //$equipoForm->handleRequest($request);
        //dump( $estacionForm->handleRequest($request));die();
        //$buscar = $this->createForm('AppBundle\Form\buscarperifericoFormType');
        // dump($estacionForm);die();
        //dump($estacionForm);die();


        if ($estacionForm->isSubmitted() && $estacionForm->isValid()) {
            dump($estacionForm->handleRequest($request));
            die();


            $estacion = $estacionForm->getData();


            $entityManager = $this->getDoctrine()->getManager();
            $estacion->setEstado('Activo');

            // $estacion->setDpto($request->get('usuarios')[0]);


            $entityManager->persist($estacion);
            $entityManager->flush();

            // $entityManager->refresh($estacion);
            $this->nombreE = $estacion->getNombreRed();
            $this->idactual = $estacion->getID();

            // $this->addFlash('success', 'Inventario Creado Correctamente con id: '.$this->idactual);
            //,array('inventario'=>$ultimo_id)
            //dump($this->idactual);
            //  die();
            // dump($this->nombreE);
            // die();
            //dump($request);
            //die();
            // return $this->redirectToRoute('adicionar_componentes', array('nombre_estacion' => $this->nombreE, 'idestacion' => $this->idactual));
            /* return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
         //'estacionForm' => $estacionForm->createView(),
         'id'=>$this->idactual

       ));*/

        } else


            return $this->render('estacion_trabajo/new_estacion.html.twig', array(
                'areas' => $area,
                'estacionForm' => $estacionForm->createView(),
                //'chasisForm'=>$equipoForm->createView(),
                'idestacion' => $this->idactual,
                'nombre_estacion' => $this->nombreE,
            ));

    }
    /**
     * Matches /blog exactly
     *
     * @Route("reportes/editar_estacionAjax", name="editar_estacionAjax")
     * @Method({"GET", "POST"})
     *
     */
    public function editarEstacionAjaxAction(Request $request)
    {
        $id=$request->get('id');
    //  dump($request);

        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        //dump($equipo1);
        // dump($request->get('modelo'));die();
        $em = $this->getDoctrine()->getManager();
        if($request->get('responsable')){
            $estacion->setResponsable($request->get('responsable'));
        }
        if($request->get('passSetup')) {
            $estacion->setPassSetup($request->get('passSetup'));
        }
        if($request->get('ip')) {
            $estacion->setIp($request->get('ip'));
        }
        if($request->get('nombreRed')) {
            $estacion->setNombreRed($request->get('nombreRed'));
        }
        if($request->get('centroCosto')) {
            $centroC=$this->getDoctrine()->getRepository('AppBundle:departamento')->findBy(['name'=>$request->get('centroCosto')])[0];
           // dump($centroC);die();
            $estacion->setCentroCosto($centroC);
        }
        if($request->get('antivirus')) {
            $estacion->setAntivirus($request->get('antivirus'));
        }

        //Creando el historial de edicion
        $incidencia = new incidencia();
        $incidencia->setTipo('Edicion');
        $incidencia->setUser($this->getUser()->getUsername());
        $incidencia->setEstado('Edicion realizada');


        $incidencia->setAsunto('---');
//        $incidencia->setRespuesta('' .
//            'serie:' . $equipo->getSerie() .
//            'modelo:' . $equipo->getModelo() . 'dato2:' . $equipo->getModelo()
//
//        );
        $incidencia->setFecha(new \DateTime("now"));
        $incidencia->setResumen('---');
        $incidencia->setInventario($estacion);


        $date = new \DateTime('now');
        $incidencia->setTipoMov(null);
        $incidencia->setEstado('Edicion realizada');
      //  $incidencia->setAsesorio($estacion->getTipoEquipo());
        $incidencia->setTecAsignado($this->getUser());
        $incidencia->setCorreo($this->getUser()->getEmail());
      //  $incidencia->setIdE($estacion->getId());
      //  $incidencia->setNumInventario($estacion);
        //$incidencia->setFechaA(date$date);
        $incidencia->setFechaA(new \DateTime("now"));

        // ... perform some action, such as saving the task to the database
        // for example, if Task is a Doctrine entity, save it!
        $entityManager = $this->getDoctrine()->getManager();
//           dump($incidencia);
//            dump($equipo);
//            die();
        $entityManager->persist($estacion);
        $entityManager->persist($incidencia);
        $entityManager->flush();

        return new JsonResponse(array('estacion' => $estacion));
    }
/*Este es el bueno!!*/
//    /**
//     * @Route("reportes/editar_inventario/{id}",name="editar_inventario")
//     */
//    public function editarInventario(Request $request, $id)
//    {
//        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
//        $form = $this->createForm(EstacionForm::class, $estacion);
//        $em = $this->getDoctrine()->getManager();
//        //  $inventario = $em->getRepository(inventario::class)->Todo($id);
//
//        $areaSel = $em->getRepository('AppBundle:area')->findBy(['id' => $estacion->getCentroCosto()->getArea()])[0];
//        $repository = $this->getDoctrine()
//            ->getRepository('AppBundle:equipo');
//        $originalEquipos = new ArrayCollection();
//        foreach ($estacion->getEquipos() as $item1) {
//            $originalEquipos->add($item1);
//        }
//        $originalComponentes = new ArrayCollection();
//        foreach ($estacion->getComponente() as $item) {
//            $originalComponentes->add($item);
//        }
//      //  dump($originalComponentes);die();
//        $chasis = $repository->createQueryBuilder('tabla')
//            ->where('tabla.estacion = :inv')
//            // ->andWhere('tabla.inventario =: idE')
//            ->setParameter('inv', $estacion)
//            ->andWhere('tabla.tipoEquipo = :asesorio')
//            ->setParameter('asesorio', 'cpuchasis')
//            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//            ->getQuery()->getResult();
//        $usuarios = $this->getDoctrine()->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
//        $areas = $this->getDoctrine()->getRepository('AppBundle:area')->findAll();
//
//
//        $form->add('Guardar', SubmitType::class, array('label' => 'Editar Estacion'));
//
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $datosform = $form->getData();
//           // dump($estacion->getComponente());
//           // dump($originalComponentes);
//            dump($datosform);
//            die();
//            // dump($datosform);die();
//            foreach ($originalEquipos as $itemE) {
//                if (false === $estacion->getEquipos()->contains($itemE)) {
//                    $estacion->removeEquipo($itemE);
//                    $itemE->setEstacion(null);
//                } else {
//                    $itemE->setEstacion($estacion);
//                }
//            }
//            foreach ($originalComponentes as $itemC) {
//                if (false === $estacion->getComponente()->contains($itemC)) {
//                    //dump($itemC);
//
////                    $estacion->removeComponente($itemC);
////                    $itemC->setTipoComponente($itemC->getTipoComponente());
////                    $itemC->setEstacion2(null);
//                    //   $em->persist($itemC);
//                   // $em->flush();
//                }
//            else{
//                $itemC->setEstacion2($estacion);
//                $itemC->setEstado('Activo');
//                $originalComponentes->add($item);
//              //  $estacion->addComponente($itemC);
//            }
//            }
//
////            foreach ($datosform->getComponente() as $item) {
////            //  dump($item);
////                if ($item->getModelo() === null && null === $item->getSerie() && null === $item->getMarca() && null === $item->getWatts() && null === $item->getSata() && null === $item->getCapacidad() && null === $item->getTipo() && null === $item->getVelocidad() && null === $item->getLga() && null === $item->getRam() && null === $item->getRanuraVideo() && null === $item->getOptico() && null === $item->getConector() && null === $item->getTipoComponente()) {
////                    $em->remove($item);
////                } else {
////                    $item->setEstado('Activo');
////                    $originalComponentes->add($item);
////                    $item->setTipoComponente($item->getTipoComponente());
////                    $item->setEstacion2($estacion);
////                }
////                $em->persist($item);
////            }
//
//            $incidencia = new incidencia();
//            $incidencia->setAsunto('Edicion de estacion');
//            $incidencia->setTipo('Edicion');
//            $incidencia->setFecha(new \DateTime());
//            $incidencia->setAsesorio($estacion->getId());
//            $incidencia->setTecAsignado($this->getUser());
//            $incidencia->setDpto($estacion->getCentroCosto());
//            $incidencia->setCorreo($this->getUser()->getEmail());
//            //$incidencia->setFechaA(date$date);
//            $incidencia->setFechaA(new \DateTime("now"));
//            $incidencia->setUser($this->getUser()->getUsername());
//            $incidencia->setEstado('Edicion realizada');
//            $entityManager = $this->getDoctrine()->getManager();
//
////      $estacion->setEquipos($originalEquipos);
////      $estacion->setComponente($originalComponentes);
//
//            $entityManager->persist($estacion);
//            //   dump($datosForm->getComponente());
////        dump($originalComponentes);
////      dump($estacion->getComponente());
////        die();
//            //  $entityManager->persist($chasis[0]);
//            $entityManager->persist($incidencia);
//            $entityManager->flush();
//            //  dump($datosForm);
//
////            dump( sizeof($chasis[0]->getComponente()));
////         //   dump($chasis[0]->getComponente()[10]);
////            dump($request);
////            die();
//            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
//            // dump($originalComponentes);die();
//        }
////       $new = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $nuevo]);
////        $dato = '';
////        if ($new != null) {
////            $dato = $new[0];
////        } else {
////            $dato;
////        }
//        $usuarios = $this->getDoctrine()->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
//        $areas = $this->getDoctrine()->getRepository('AppBundle:area')->findAll();
////        dump($estacion);
////        die();
//        return $this->render('estacion_trabajo/editar_inventario.html.twig', ["form" => $form->createView(), "inventario" => $estacion
//            , 'nuevo' => '', "chasis" => $chasis[0], 'tipo_edicion' => 'edicion', 'usuarios' => $usuarios, 'areas' => $areas, 'areaSeleccionada' => $areaSel, "lista_componentes" => $estacion->getComponente()]);
//    }

    /**
     * @Route("reportes/editar_inventario/{id}",name="editar_inventario")
     */
    public function editarInventario(Request $request, $id)
    {
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        $form = $this->createForm(EstacionForm::class, $estacion);
        $em = $this->getDoctrine()->getManager();
        //  $inventario = $em->getRepository(inventario::class)->Todo($id);
        $areaSel = $em->getRepository('AppBundle:area')->findBy(['id' => $estacion->getCentroCosto()->getArea()])[0];
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');
        $originalEquipos = new ArrayCollection();
        foreach ($estacion->getEquipos() as $item1) {
            $originalEquipos->add($item1);
        }
        $originalComponentes = new ArrayCollection();
        foreach ($estacion->getComponente() as $item) {
            $originalComponentes->add($item);
        }
        $chasis = $repository->createQueryBuilder('tabla')
            ->where('tabla.estacion = :inv')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('inv', $estacion)
            ->andWhere('tabla.tipoEquipo = :asesorio')
            ->setParameter('asesorio', 'cpuchasis')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery()->getResult();
        $usuarios = $this->getDoctrine()->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $areas = $this->getDoctrine()->getRepository('AppBundle:area')->findAll();


        $form->add('Guardar', SubmitType::class, array('label' => 'Editar Estacion'));


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datosform = $form->getData();
            dump($originalComponentes);
            dump($datosform);
            die();
            // dump($datosform);die();
            foreach ($originalEquipos as $itemE) {
                if (false === $estacion->getEquipos()->contains($itemE)) {
                    $estacion->removeEquipo($itemE);
                    $itemE->setEstacion(null);
                } else {
                    $itemE->setEstacion($estacion);
                }
            }
            foreach ($originalComponentes as $itemC) {
                if (false === $estacion->getComponente()->contains($itemC)) {
                    // dump($itemE);
                    // $em->remove($itemE);
                    $estacion->removeComponente($itemC);
                    $itemC->setTipoComponente($itemC->getTipoComponente());
                    $itemC->setEstacion2(null);
                    //   $em->persist($itemC);
                    $em->flush();
                }
//            else{
//                $itemC->setEstacion2($estacion);
//              //  $estacion->addComponente($itemC);
//            }
            }
            foreach ($datosform->getComponente() as $item) {
                if ($item->getModelo() === null && null === $item->getSerie() && null === $item->getMarca() && null === $item->getWatts() && null === $item->getSata() && null === $item->getCapacidad() && null === $item->getTipo() && null === $item->getVelocidad() && null === $item->getLga() && null === $item->getRam() && null === $item->getRanuraVideo() && null === $item->getOptico() && null === $item->getConector() && null === $item->getTipoComponente()) {
                    $em->remove($item);
                } else {
                    $item->setEstado('Activo');
                    $item->setTipoComponente($item->getTipoComponente());
                    $item->setEstacion2($estacion);
                }
                $em->persist($item);
            }

            $incidencia = new incidencia();
            $incidencia->setAsunto('Edicion de estacion');
            $incidencia->setTipo('Edicion');
            $incidencia->setFecha(new \DateTime());
            $incidencia->setAsesorio($estacion->getId());
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setDpto($estacion->getCentroCosto());
            $incidencia->setCorreo($this->getUser()->getEmail());
            //$incidencia->setFechaA(date$date);
            $incidencia->setFechaA(new \DateTime("now"));
            $incidencia->setUser($this->getUser()->getUsername());
            $incidencia->setEstado('Edicion realizada');
            $entityManager = $this->getDoctrine()->getManager();

//      $estacion->setEquipos($originalEquipos);
//      $estacion->setComponente($originalComponentes);

            $entityManager->persist($estacion);
            //   dump($datosForm->getComponente());
//        dump($originalComponentes);
//      dump($estacion->getComponente());
//        die();
            //  $entityManager->persist($chasis[0]);
            $entityManager->persist($incidencia);
            $entityManager->flush();
            //  dump($datosForm);

//            dump( sizeof($chasis[0]->getComponente()));
//         //   dump($chasis[0]->getComponente()[10]);
//            dump($request);
//            die();

            return $this->redirectToRoute('datos_estacion', ["id" => $id]);
            // dump($originalComponentes);die();
        }
//       $new = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $nuevo]);
//        $dato = '';
//        if ($new != null) {
//            $dato = $new[0];
//        } else {
//            $dato;
//        }
//        dump($originalComponentes);
//        dump($estacion->getComponente());
//        die();
        $usuarios = $this->getDoctrine()->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $areas = $this->getDoctrine()->getRepository('AppBundle:area')->findAll();

        return $this->render('estacion_trabajo/editar_inventario.html.twig', ["form" => $form->createView(), "inventario" => $estacion
            , 'nuevo' => '', "chasis" => $chasis[0], 'tipo_edicion' => 'edicion', 'usuarios' => $usuarios, 'areas' => $areas, 'areaSeleccionada' => $areaSel, "lista_componentes" => $estacion->getComponente()]);

    }


    /**
     *
     * @Route("reportes/nuevo_inventario_estacion/{id}/{nuevo}", name="nuevo_inventario_estacion")
     * @return Response
     */
    public function nuevoIEstacionAction(Request $request, $id, $nuevo = null)
    {
        $em = $this->getDoctrine()->getManager();
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);
        $inventario = $em->getRepository(inventario::class)->Todo($id)[0];
        $chasis = null;
        $inventarioOriginal = new inventario();
        $inventarioOriginal->setResponsable($inventario->getResponsable());
        $inventarioOriginal->setIP($inventario->getIp());
        $inventarioOriginal->setNombreRed($inventario->getNombreRed());
        $inventarioOriginal->setPassSetup($inventario->getPassSetup());
        $inventarioOriginal->setEstado('Inactivo');
        $inventarioOriginal->setActivo('No');
        $inventarioOriginal->setTecnico($inventario->getTecnico());
        $inventarioOriginal->setJefeInformatica($inventario->getJefeInformatica());
        $inventarioOriginal->setCentroCosto($inventario->getCentroCosto());
        $em->persist($inventarioOriginal);
//dump($inventarioOriginal->getComponente()[0]);die();
        foreach ($inventario->getEquipos() as $i) {
            if ($i->getTipoEquipo() == 'cpuchasis') {
                $chasis = $i;
            }
        }
        $temporal = new ArrayCollection();
        $temporalComponentes = new ArrayCollection();
        $chasis_viejo = null;
        foreach ($inventario->getEquipos() as $equipo) {
            $e = new equipo_de_inventarios();
            $e->setNumInventario($equipo->getNumInventario());
            $e->setModelo($equipo->getModelo());
            $e->setSerie($equipo->getSerie());
            $e->setMarca($equipo->getMarca());
            $e->setInventario($inventarioOriginal);
            $e->setSello($equipo->getSello());
            $e->setCapacidad($equipo->getCapacidad());
            $e->setColor($equipo->getColor());
            $e->setTipo($equipo->getTipo());
            $e->setTipoTonerCartucho($equipo->getTipoTonerCartucho());
            $e->setLcd($e->getLcd());
            $e->setTipoEquipo($equipo->getTipoEquipo());
            if ($e->getTipoEquipo() == 'cpuchasis') {
                $chasis_viejo = $e;
                // $chasis_viejo->setComponente($chasis->getComponente());
            }
            $temporal->add($e);
        }

        foreach ($inventario->getComponente() as $componente) {
            $comp = new componente_de_inventarios();
            $comp->setModelo($componente->getModelo());
            $comp->setSerie($componente->getSerie());
            $comp->setMarca($componente->getMarca());
            $comp->setInventarioC($inventarioOriginal);
            $comp->setEstado($componente->getEstado());
            $comp->setCapacidad($componente->getCapacidad());
            $comp->setWatts($componente->getWatts());
            $comp->setTipo($componente->getTipo());
            $comp->setSata($componente->getSata());
            $comp->setLga($componente->getLga());
            $comp->setRam($componente->getRam());
            $comp->setOptico($componente->getOptico());
            $comp->setConector($componente->getConector());
            $comp->setTipoComponente($componente->getTipoComponente());
            $temporalComponentes->add($comp);
        }


        // dump($temporalComponentes);die();
//    if ($chasis != null) {
//      $chasisform = $this->createForm(\AppBundle\Form\equipoFomType::class, $chasis);
//      $c = $chasisform->createView();
//    }

        $form = $this->createForm(EstacionForm::class, $estacion);
//        $inventario[0]->setEquipos($equipos_en_inventarioViejo);
        //$em->persist($inventario[0]);
        // dump($inventario[0]);die();
        //$form->add('Guardar', SubmitType::class, array('label' => 'Guardar inventario'));
        $form->handleRequest($request);
//    $originalComponentes = new ArrayCollection();
//    foreach ($chasis->getComponente() as $item) {
//      $originalComponentes->add($item);
//    }

        $originalEquipos = new ArrayCollection();
        foreach ($estacion->getEquipos() as $item1) {
            $originalEquipos->add($item1);
        }
        $originalComponentes = new ArrayCollection();
        foreach ($estacion->getComponente() as $item) {
            $originalComponentes->add($item);
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $datosForm = $form->getData();
            //$chasisData = $chasisform->getData();
            $em->persist($inventarioOriginal);
            //  $em->flush();
            foreach ($estacion->getComponente() as $item) {
//                  dump($item);
                if ($item->getModelo() === null && null === $item->getSerie() && null === $item->getMarca() && null === $item->getWatts() && null === $item->getSata() && null === $item->getCapacidad() && null === $item->getTipo() && null === $item->getVelocidad() && null === $item->getLga() && null === $item->getRam() && null === $item->getRanuraVideo() && null === $item->getOptico() && null === $item->getConector() && null === $item->getTipoComponente()) {
                    $em->remove($item);
                    $item->setEstacion2($inventario);
                    // $item->setCpu(null);
//                dump($item);
//                die();
                } else {
                    //dump($item->getTipoComponente());die();
                    $item->setEstado('Activo');
                    $item->setTipoComponente($item->getTipoComponente());
                    $item->setEstacion2($estacion);
                    //   $item->setCpu($chasis[0]);
                }
                //  dump($item);
                $em->persist($item);
                //  dump($item);

            }
//        foreach ($estacion->getEquipos() as $item2) {
////                  dump($item);
//            if ($item2->getEstacion2() === null ) {
//                $em->remove($item);
//                $item2->setEstacion2($inventario);
//                // $item->setCpu(null);
////                dump($item);
////                die();
//            } else {
//                //dump($item->getTipoComponente());die();
//                $item2->setEstado('Activo');
//                $item2->setTipoEquipo($item2->getTipoEquipo());
//                $item2->setEstacion2($estacion);
//                //   $item->setCpu($chasis[0]);
//            }
//            //  dump($item);
//            $em->persist($item2);
//            //  dump($item);
//
//        }

            $inventarioOriginal->setEquiposEnInventario($temporal);
            $inventarioOriginal->setComponenteInventario($temporalComponentes);
//      $inventario->setEquipos($datosForm->getEquipos());
//      $inventario->setComponente($datosForm->getComponente());
            $fecha_actual = new \DateTime("now");
            $inventario->setFechacreacion($fecha_actual);
            // $inventario->set
            $inventario->setActivo('Si');
            $inventario->setEstado('Activo');

            $zonaHoraria = new \DateTimeZone('America/Cuiaba');

//      // dump($equipos_en_inventarioViejo);die();
//      foreach ($originalComponentes as $itemE) {
//        if (false === $chasis->getComponente()->contains($itemE)) {
//          $em->remove($itemE);
//          $em->flush();
//        }
//      }
            // $chasis->setEstacion($nuevoInventario);
            // dump($chasis);die();
//      foreach ($chasis->getComponente() as $item) {
//        if ($item->getModelo() === null && null === $item->getSerie() && null === $item->getMarca() && null === $item->getWatts() && null === $item->getSata() && null === $item->getCapacidad() && null === $item->getTipo() && null === $item->getVelocidad() && null === $item->getLga() && null === $item->getRam() && null === $item->getRanuraVideo() && null === $item->getOptico() && null === $item->getConector() && null === $item->getTipoComponente()) {
//          $em->remove($item);
//        } else {
//          //dump($item->getTipoComponente());die();
//          //$item->setEstado('Activo');
//          // $item->setEstacion($inventario);
//          $item->setTipoComponente($item->getTipoComponente());
//          $item->setCpu($chasis);
//
//        }
//        $em->persist($item);
//      }
//
            foreach ($datosForm->getEquipos() as $e) {
                switch ($e->getTipoEquipo()) {
                    case 'cpuchasis':
                        $chasis = $e;
                        $chasis->setEstacion($inventario);
////          $nuevoInventario->addEquipo($chasis);
////////            $em->persist($chasis);
////////            foreach ($chasis->getComponente() as $c) {
////////              switch ($c->getTipoComponente()) {
////////                case 'fuente':
////////                  $fuente = $c;
////////                  break;
////////                case 'motherboard':
////////                  $mother = $c;
////////                  break;
////////                case 'mouse':
////////                  $mouse = $c;
////////                  break;
////////                case 'lector':
////////                  $lector = $c;
////////                  break;
////////                case 'teclado':
////////                  $teclado = $c;
////////                  break;
////////                case 'bocina':
////////                  $bocina = $c;
////////                  break;
////////                case 'microprocesador':
////////                  $micro = $c;
////////                  break;
////////                case 'ram':
////////                  $ram = $c;
////////                  break;
////////                case 'hdd':
////////                  $hdd = $c;
////////                  break;
////////              }
////////
////////            }
//          break;
                    case 'backup':
                        $backup = $e;
                        $backup->setEstacion($inventario);
                        $em->persist($backup);
                        break;
                    case 'impresora':
                        $impresora = $e;
                        //$impresora->setEstacion($nuevoInventario);
                        // $em->persist($impresora);
                        break;
                    case 'monitor':
                        $monitor = $e;
                        $monitor->setEstacion($inventario);
                        $em->persist($monitor);
                        break;
                    case 'scanner':
                        $scanner = $e;
                        $scanner->setEstacion($inventario);
                        $em->persist($scanner);
                        break;
                    case 'estabilizador':
                        $estabilizador = $e;
                        $estabilizador->setEstacion($inventario);
                        $em->persist($estabilizador);
                        break;
                }
//
            }
            $incidencia = new incidencia();
            $incidencia->setAsunto('Nuevo Inventario');
            $incidencia->setTipo('Nuevo Inventario');
            $incidencia->setFecha(new \DateTime('now', $zonaHoraria));
            $incidencia->setAsesorio($inventario->getId());
            $incidencia->setTecAsignado($this->getUser());
            $incidencia->setDpto($inventario->getCentroCosto());
            $incidencia->setCorreo($this->getUser()->getEmail());
            //$incidencia->setFechaA(date$date);
            $incidencia->setFechaA(new \DateTime("now"));
            $incidencia->setUser($this->getUser()->getUsername());
            $incidencia->setEstado('Nuevo inventario  Realizado');

//        dump($temporalComponentes);
//        dump($inventarioOriginal);
//        dump($inventario);die();
//


            $em->persist($incidencia);

            $chasis = null;
//            foreach ($nuevoInventario->getEquipos() as $i) {
//                //$i->setEstacion($inventario[0]);
//                $inventario[0]->addEquipo($i);
//            }
//           $em->persist($inventario[0]);
            $em->flush();

//            dump($inventario[0]->getEquipos());
//            dump($nuevoInventario->getEquipos());
//            dump($equipos_en_inventarioViejo);
//            die();
//            dump(sizeof($inventario[0]->getEquipos()));
//            dump($nuevoInventario->getEquipos()[0]);
//            die();
//            dump($inventario[0]->getEquiposEnInventario());
//            dump($nuevoInventario[0]->getEquiposEnInventario());

            // dump($equipos_en_inventarioViejo);dump($inventario[0]->getEquipos());die();
            return $this->redirectToRoute('datos_estacion', ["id" => $inventario->getId()]);
        }
        $new = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $nuevo]);
        $dato = '';
        if ($new != null) {
            $dato = $new[0];
        } else {
            $dato;
        }
        $nuevoInventarioArray = [];
        // $nuevoInventarioArray[0] = $nuevoInventario;
        //dump($nuevoInventarioArray);die();
        $areaSel = $em->getRepository('AppBundle:area')->findBy(['id' => $inventario->getCentroCosto()->getArea()])[0];
        $areas = $em->getRepository('AppBundle:area')->findAll();
        $usuarios = $this->getDoctrine()->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        return $this->render('estacion_trabajo/editar_inventario.html.twig', ["form" => $form->createView(), "inventario" => $inventario
            , 'chasis' => $chasis, 'tipo_edicion' => 'inventario_nuevo', 'areaSeleccionada' => $areaSel, 'areas' => $areas,
            //'chasisform' => $c,
            "lista_componentes" => $inventario->getComponente(),
            'usuarios' => $usuarios, 'nuevo' => $dato]);
    }


    /**
     * @Route("/reportes/estacion/list/{nombre_estacion}/{idestacion}", name="estacionLista")
     *
     * @return Response
     */
    public function listAction($nombre_estacion, $idestacion, $tipo)
    {


        $this->tipo = $tipo;
        $componente_seleccionado = $this->tipo;
        $entityManager = $this->getDoctrine()->getManager();
        $this->nombreE = $nombre_estacion;
        //$this->idactual = $idestacion;

        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $lista_temporal = $applicationRepository->findAll();


        if (empty($this->tipo) != true) {
            // dump($this->tipo);
            //die();
            //$componente_seleccionado = $this->tipo;


            if ($this->tipo == 'backup') {

                $applicationRepository = $entityManager->getRepository('AppBundle:backup');

                $incidencias = $applicationRepository->findBy(['estado' => 1]);
//dump($incidencias);die();
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/adicionar_componentes.html.twig',
                    [
                        'nombre_estacion' => $this->nombreE,
                        'componente' => $this->tipo,
                        'incidencias' => $incidencias,
                        'idestacion' => $idestacion,
                        'lista' => $lista_temporal
                    ]);

            } else if ($this->tipo == 'estabilizador') {

                $applicationRepository = $entityManager->getRepository('AppBundle:estabilizador');
                $incidencias = $applicationRepository->findBy(['estado' => 1]);

//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/adicionar_componentes.html.twig',
                    [
                        'nombre_estacion' => $this->nombreE,
                        'componente' => $this->tipo,
                        'incidencias' => $incidencias,
                        'idestacion' => $idestacion,
                        'lista' => $lista_temporal
                    ]);

            } else if ($this->tipo == 'cpuchasis') {


                $applicationRepository = $entityManager->getRepository('AppBundle:cpuchasis');

                $incidencias = $applicationRepository->findBy(['estado' => 1]);
                //dump($incidencias);die();
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/adicionar_componentes.html.twig',
                    [
                        'nombre_estacion' => $this->nombreE,
                        'componente' => $this->tipo,
                        'incidencias' => $incidencias,
                        'idestacion' => $idestacion,
                        'lista' => $lista_temporal

                    ]);

            } else if ($this->tipo == 'impresora') {

                $applicationRepository = $entityManager->getRepository('AppBundle:impresora');

                $incidencias = $applicationRepository->findBy(['estado' => 1]);

//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/adicionar_componentes.html.twig',
                    [
                        'nombre_estacion' => $this->nombreE,
                        'componente' => $this->tipo,
                        'incidencias' => $incidencias,
                        'idestacion' => $idestacion,
                        'lista' => $lista_temporal

                    ]);

            } else if ($this->tipo == 'monitor') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:monitor');

                $incidencias = $applicationRepository->findBy(['estado' => 1]);

//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/adicionar_componentes.html.twig',
                    [
                        'nombre_estacion' => $this->nombreE,
                        'componente' => $this->tipo,
                        'incidencias' => $incidencias,
                        'idestacion' => $idestacion,
                        'lista' => $lista_temporal

                    ]);

            }
        } else
            return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
                "filters" => $this->filters,
                'nombre_estacion' => $this->nombreE,
                'idestacion' => $idestacion,
                "pagination" => $this->pagination));
        /* return $this->render(
       'estacion_trabajo/nueva_estacion.html.twig',
       [
         //  'componente' => $componente_seleccionado,
         "filters" => $this->filters,
         "pagination" => $this->pagination,
        // 'estacionForm'=>$estacionForm,
       ]
     );*/
    }


    /**
     * @Route("/reportes/estacion/list2/{nombre_estacion}/{idestacion}", name="estacion2Lista")
     *
     * @return Response
     */
    public function list2Action($nombre_estacion, $idestacion, $numI)
    {


        // $this->tipo = $tipo;
        $componente_seleccionado = $this->tipo;
        $entityManager = $this->getDoctrine()->getManager();
        $this->nombreE = $nombre_estacion;
        //$this->idactual = $idestacion;

        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $lista_temporal = $applicationRepository->findAll();


        if (empty($numI) != true) {
            // dump($this->tipo);
            //die();
            //$componente_seleccionado = $this->tipo;


            $backup1 = $entityManager->getRepository('AppBundle:backup');
            $estabilizador1 = $entityManager->getRepository('AppBundle:estabilizador');
            $equipo1 = $entityManager->getRepository('AppBundle:cpuchasis');
            $imp1 = $entityManager->getRepository('AppBundle:impresora');
            $mo1 = $entityManager->getRepository('AppBundle:monitor');
            $scan1 = $entityManager->getRepository('AppBundle:scanner');

            $query = $entityManager->createQuery(
                'SELECT u FROM AppBundle:estabilizador u JOIN AppBundle:backup b
        WHERE  u.numInventario =' . $numI . 'or  b.numInventario=' . $numI . '
        
        ');

            $query2 = $entityManager->createQuery(
                'SELECT name FROM information_schema.tables where u.numInventario =' . $numI . 'or  b.numInventario=' . $numI . '
        
        ');
            $conn = $this->get('database_connection');
//run a query
            $users = $conn->fetchAll('SELECT TABLE_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
WHERE 
COLUMN_NAME = "num_inventario" 



and num_inventario=' . $numI . '
ORDER BY DATA_TYPE
 ');
            $incidencias = $query->execute();

            dump($users);
            die();
            /* $paginator = $this->get('knp_paginator');
       $pagination = $paginator->paginate(
         $query,
         $request->query->getInt('page', 1), 1
       );*/

            // $incidencias = $applicationRepository->findBy(['estado' => 1]);

            // dump($incidencias);die();
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
            return $this->render('estacion_trabajo/adicionar_componentes.html.twig',
                [
                    'nombre_estacion' => $this->nombreE,
                    'componente' => 'y',
                    'incidencias' => $incidencias,
                    'idestacion' => $idestacion,
                    'lista' => $lista_temporal
                ]);


        } else
            return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
                "filters" => $this->filters,
                'componente' => '',
                'nombre_estacion' => $this->nombreE,
                'idestacion' => $idestacion,
                "pagination" => $this->pagination));
        /* return $this->render(
       'estacion_trabajo/nueva_estacion.html.twig',
       [
         //  'componente' => $componente_seleccionado,
         "filters" => $this->filters,
         "pagination" => $this->pagination,
        // 'estacionForm'=>$estacionForm,
       ]
     );*/
    }


    /**
     * @Route("/reportes/estacion/filter_equipo/{idestacion}",name="e_filter")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function filterAction(Request $request, $idestacion)
    {
        // dump($request);die();

        $entityManager = $this->getDoctrine()->getManager();
        $select = $request->request->get('componente');
        $numInv = $request->request->get('numI');
        //dump($numInv);die();
        $applicationRepository = $entityManager->getRepository('AppBundle:inventario');
        $estacion = $applicationRepository->find($idestacion);
        $nombreEstacion = $estacion->getNombreRed();

        $this->nombreE = $nombreEstacion;
        // dump($nombreEstacion);
        //  die();

        if ($select == 1) {

            return $this->listAction($this->nombreE, $idestacion, $numInv, 'backup');

        } else if ($select == 2) {

            return $this->listAction($this->nombreE, $idestacion, $numInv, 'estabilizador');
        } else if ($select == 3) {

            return $this->listAction($this->nombreE, $idestacion, $numInv, 'cpuchasis');
        } else if ($select == 4) {

            return $this->listAction($this->nombreE, $idestacion, $numInv, 'impresora');
        } else if ($select == 5) {

            return $this->listAction($this->nombreE, $idestacion, $numInv, 'monitor');
        } /* if ($numInv != null) {
      // dump("holaaa");die();
      return $this->list2Action($this->nombreE, $idestacion, $numInv);
    } */
        else
            $this->addFlash('success', 'Usted debe seleccionar un periférico');

        return $this->redirectToRoute('adicionar_componentes', ['nombre_estacion' => $this->nombreE, 'idestacion' => $idestacion, 'filters' => $this->filters,
            'pagination' => $this->pagination]);
    }


    /**
     * @Route("/incidencia/filter_estacion",name="estacionE_filter")
     * @Method("POST")
     * @param Request $request
     *
     * @return Response
     */
    public function filterEstacionAction(Request $request)
    {

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

        if ($request->request->get('reset') != 1) {
            $this->filters = [
                'id' => $request->request->get('id'),
                'dpto' => $request->request->get('dpto'),
                'estado' => $request->request->get('estado')

            ];
        } else $this->filters = [];

        if ($request->request->has('limit')) {
            $this->pagination = [
                'limit' => $request->request->get('limit'),
                'offset' => $request->request->get('offset'),
            ];
        }

        return $this->listEstAction();
    }

    /**
     * @Route("/estacion_l/list", name="estaciona_list")
     * @Method("GET")
     * @return Response
     */
    public function listEstAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:inventario');
        $lista = $applicationRepository->findAll();

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


        if (empty($this->filters) != true) {


            $incidencias = $applicationRepository->findByFiltersAndPaginate(
                $this->filters,
                null,
                $this->pagination,
                $lista
            );

            return $this->render(
                'estacion_trabajo/lista_estaciones.html.twig',
                [
                    'areas' => $area,
                    'incidencias' => $incidencias,
                    "filters" => $this->filters,
                    "pagination" => $this->pagination
                ]
            );
        }
        $incidencias = $applicationRepository->findAll();

        return $this->render(
            'estacion_trabajo/lista_estaciones.html.twig',
            [
                'areas' => $area,
                'incidencias' => $incidencias,
                "filters" => $this->filters,
                "pagination" => $this->pagination
            ]
        );
    }


    /**
     * @Route("/estacion/ver_datos_componente/{id}",name="ver_datos_componente")
     */
    public function show_DatosComponenteAction($id, Request $request)
    {
        $compo = $request->get('componente');
        $tipo = $request->get('tipo');


        $entityManager = $this->getDoctrine()->getManager();
        $componente = $entityManager->getRepository(componente::class)->find($id);


        if (!$componente) {
            throw $this->createNotFoundException(
                'No existe ningun equipo guardado con este id' . $id
            );
        }
        return $this->render('estacion_trabajo/ver_datos_componente.html.twig', ['datos' => $componente, 'id' => $id, 'tipo' => $componente->getTipoComponente(), 'historial' => '']);
    }




    /**
     * @Route("/estacion/editar_datos_periferico/{id}/{tipo}",name="editar_datos_periferico")
     */
    public function editar_DatosPerifericoAction($id, Request $request, $tipo)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $form = '';
        if ($tipo == 'backup') {

            $backup = $this->getDoctrine()->getRepository('AppBundle:backup')->find($id);
            $form = $this->createForm(\AppBundle\Form\backupType::class, $backup);
            $form->add('Guardar', SubmitType::class, array('label' => 'Editar equipo'));
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {


                $backup = $form->getData();


                $entityManager->persist($backup);
                $entityManager->flush();
                $this->addFlash('success', 'Equipo Editado Correctamente');
                return $this->redirectToRoute('lista_equipos');
            }

        } else if ($tipo == 'bocinas') {

            $bocina = $this->getDoctrine()->getRepository('bocinas')->find($id);
            $form = $this->createForm(\AppBundle\Form\bocinaType::class, $bocina);
            $form->add('Guardar', SubmitType::class, array('label' => 'Editar equipo'));
            if ($form->isSubmitted() && $form->isValid()) {


                $bocina = $form->getData();


                $entityManager->persist($bocina);
                $entityManager->flush();
                $this->addFlash('success', 'Equipo Editado Correctamente');
                return $this->redirectToRoute('ver_datos_periferico', ['id' => $id]);
            }
        } else if ($tipo == 'impresora') {

            $impresora = $this->getDoctrine()->getRepository('AppBundle:impresora')->find($id);

            //dump($impresora);
            //die();
            $impresoraform = $this->createForm(\AppBundle\Form\impresoraFormType::class, $impresora);
            $impresoraform->add('Guardar', SubmitType::class, array('label' => 'Editar equipo'));
            // dump($request);
            $form = $impresoraform;
            // die();

            if ($impresoraform->isSubmitted() && $impresoraform->isValid()) {
                // dump($impresoraform);
                // die();

                $impresora = $impresoraform->getData();


                $entityManager->persist($impresora);
                $entityManager->flush();
                $this->addFlash('success', 'Equipo Editado Correctamente');
                return $this->redirectToRoute('ver_datos_periferico', ['id' => $id]);
            }
            // dump("no entre");
            //die();
        } else if ($tipo == 'monitor') {

            $monitor = $this->getDoctrine()->getRepository('AppBundle:monitor')->find($id);
            $monitorform = $this->createForm(\AppBundle\Form\monitorType::class, $monitor);
            $monitorform->add('Guardar', SubmitType::class, array('label' => 'Editar equipo'));
            if ($monitorform->isSubmitted() && $monitorform->isValid()) {
                // dump($impresoraform);
                // die();

                $impresora = $monitorform->getData();


                $entityManager->persist($monitor);
                $entityManager->flush();
                $this->addFlash('success', 'Equipo Editado Correctamente');
                return $this->redirectToRoute('ver_datos_periferico', ['id' => $id]);
            }
        } else if ($tipo == 'mouse') {

            $mouse = $this->getDoctrine()->getRepository('AppBundle:mouse')->find($id);
            $form = $this->createForm(\AppBundle\Form\mouseType::class, $mouse);
            $form->add('Guardar', SubmitType::class, array('label' => 'Editar equipo'));
            if ($form->isSubmitted() && $form->isValid()) {
                // dump($impresoraform);
                // die();

                $impresora = $monitorform->getData();


                $entityManager->persist($mouse);
                $entityManager->flush();
                $this->addFlash('success', 'Equipo Editado Correctamente');
                return $this->redirectToRoute('ver_datos_periferico', ['id' => $id]);
            }
        } else if ($tipo == 'teclado') {

            $teclado = $this->getDoctrine()->getRepository('AppBundle:teclado')->find($id);
            $form = $this->createForm(\AppBundle\Form\tecladoType::class, $teclado);
            $form->add('Guardar', SubmitType::class, array('label' => 'Editar equipo'));
        } else if ($tipo == 'cpuchasis') {

            //dump($request);die();

            return $this->redirectToRoute('editar_chasisE', ['id' => $id]);
            // return $this->render('estacion_trabajo/editar_datos_' . $tipo . '.html.twig', ['form' => $form->createView(), 'id' => $id, 'tipo' => $tipo,'chasis'=>$cpu
            // ]);
        }


        if (!$form) {
            throw $this->createNotFoundException(
                'No existe ningun periferico guardado con este id' . $id
            );
        }
        // dump($this->tipo);
        // die();
        return $this->render('estacion_trabajo/editar_datos_' . $tipo . '.html.twig', ['form' => $form->createView(), 'id' => $id, 'tipo' => $tipo]);
    }

    /**
     * @Route("/estacion/editar_chasis/{id}",name="editar_chasisE")
     */
    public function editarChasis1Action(Request $request, $id)
    {
        $cpu = $this->getDoctrine()->getRepository('AppBundle:cpuchasis')->find($id);

        if (!$cpu) {
            throw $this->createNotFoundException('No se ha encontrado ningun chasis con este id' . $id);
        }

        //Arreglos con los objetos que trae el chasis
        $originalBoard = new ArrayCollection();
        $bocina = new ArrayCollection();
        $fuente = new ArrayCollection();
        $micro = new ArrayCollection();
        $memoria_ram = new ArrayCollection();
        $discoD = new ArrayCollection();
        $lectorCD = new ArrayCollection();
        $raton = new ArrayCollection();
        $teclado1 = new ArrayCollection();
        $tarjeta_video = new ArrayCollection();

//dump($cpu);
//die();
        foreach ($cpu->getboard() as $item) {
            $originalBoard->add($item);
        }
        foreach ($cpu->getBocina1() as $item) {
            $bocina->add($item);
        }
        foreach ($cpu->getFuente() as $item) {
            $fuente->add($item);
        }
        foreach ($cpu->getDiscoD() as $item) {
            $discoD->add($item);
        }
        foreach ($cpu->getLectorCD() as $item) {
            $lectorCD->add($item);
        }
        foreach ($cpu->getMicro() as $item) {
            $micro->add($item);
        }
//    foreach ($cpu->getRaton() as $item) {
//      $raton->add($item);
//    }
        foreach ($cpu->getMemoriaRam() as $item) {
            $memoria_ram->add($item);
        }
//    foreach ($cpu->getTarjetaVideo() as $item) {
//      $tarjeta_video->add($item);
//    }
//    foreach ($cpu->getTeclado1() as $item) {
//      $teclado1->add($item);
//    }
//    foreach ($cpu->getboard() as $item) {
//      $originalBoard->add($item);
//    }
        foreach ($cpu->getBocina1() as $item) {
            $bocina->add($item);
        }
        foreach ($cpu->getFuente() as $item) {
            $fuente->add($item);
        }
        foreach ($cpu->getDiscoD() as $item) {
            $discoD->add($item);
        }
        foreach ($cpu->getLectorCD() as $item) {
            $lectorCD->add($item);
        }
        foreach ($cpu->getMicro() as $item) {
            $micro->add($item);
        }
//    foreach ($cpu->getRaton() as $item) {
//      $raton->add($item);
//    }
        foreach ($cpu->getMemoriaRam() as $item) {
            $memoria_ram->add($item);
        }
//    foreach ($cpu->getTarjetaVideo() as $item) {
//      $tarjeta_video->add($item);
//    }
//    foreach ($cpu->getTeclado1() as $item) {
//      $teclado1->add($item);
//    }

//    dump($teclado1);
//    die();
        //dump($cpu->getInv());
        // die();
        $form = $this->createForm(\AppBundle\Form\chasisFormType::class, $cpu);
        $form->add('Guardar', SubmitType::class, array('label' => 'Editar chasis'));
        $em = $this->getDoctrine()->getManager();
//    $inventario = $cpu->getInv();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            dump($cpu);
            die();
            foreach ($cpu->getboard() as $b) {
                $b->setMotherboard($cpu);
                $b->setInventario($inventario);
                $b->setEstado('Activo');
                $em->persist($b);
                //dump($b);die();

            }

            foreach ($cpu->getFuente() as $f) {
                $f->setFuentes($cpu);
                $f->setInventario($inventario);
                $em->persist($f);
                //dump($b);die();

            }

            foreach ($cpu->getMicro() as $m) {
                $m->setMicrop($cpu);
                $m->setInventario($inventario);
                $em->persist($m);
                //dump($b);die();

            }
            foreach ($cpu->getDiscoD() as $h) {
                $h->setHdd($cpu);
                $h->setInventario($inventario);
                $em->persist($h);
                //dump($b);die();

            }
            foreach ($cpu->getLectorCD() as $l) {
                $l->setLector($cpu);
                $l->setInventario($inventario);
                $em->persist($l);
                //dump($b);die();

            }
            foreach ($cpu->getRaton() as $mo) {
                $mo->setMouse($cpu);
                $mo->setInventario($inventario);
                $em->persist($mo);
                //dump($b);die();

            }
            foreach ($cpu->getTeclado1() as $t) {
                $t->setTeclado($cpu);
                $t->setInventario($inventario);
                $em->persist($t);
                //dump($b);die();

            }
            foreach ($cpu->getBocina1() as $bo) {
                $bo->setBocinas($cpu);
                $bo->setInventario($inventario);
                $em->persist($bo);
                //dump($b);die();

            }
            foreach ($cpu->getTarjetaVideo() as $tv) {
                $tv->setTarjetaV($cpu);
                $tv->setInventario($inventario);
                $em->persist($tv);
                //dump($b);die();

            }
            foreach ($cpu->getScan() as $s) {
                $s->setScanner($cpu);
                $s->setInventario($inventario);
                $em->persist($s);
                //dump($b);die();

            }

            $cpu->setInventario($inventario);
            $em->persist($cpu);
            $em->flush();

            dump($cpu);
            die();

            $datos = $this->getDoctrine()
                ->getRepository('AppBundle:cpuchasis')
                ->find($id);
            $incidencias = $this->getDoctrine()->getRepository('AppBundle:incidencia')->findBy(['inventario' => $id]);


            $fuente = $this->getDoctrine()
                ->getRepository('AppBundle:fuente')
                ->findOneBy(['fuentes' => $id]);

            $board = $this->getDoctrine()
                ->getRepository('AppBundle:motherboard')
                ->findOneBy(['motherboard' => $id]);

            $micro = $this->getDoctrine()
                ->getRepository('AppBundle:microprocesador')
                ->findOneBy(['microp' => $id]);

            $ram = $this->getDoctrine()
                ->getRepository('AppBundle:ram')
                ->findOneBy(['ram' => $id]);

            $hdd = $this->getDoctrine()
                ->getRepository('AppBundle:hdd')
                ->findOneBy(['hdd' => $id]);

            $mouse = $this->getDoctrine()
                ->getRepository('AppBundle:mouse')
                ->findOneBy(['mouse' => $id]);

            $teclado = $this->getDoctrine()
                ->getRepository('AppBundle:teclado')
                ->findOneBy(['teclado' => $id]);

            $bocina = $this->getDoctrine()
                ->getRepository('AppBundle:bocinas')
                ->findOneBy(['bocinas' => $id]);

            $tarjetaV = $this->getDoctrine()
                ->getRepository('AppBundle:tarjeta_video')
                ->findOneBy(['tarjetaV' => $id]);

            $scanner = $this->getDoctrine()
                ->getRepository('AppBundle:scanner')
                ->findOneBy(['scanner' => $id]);

            $lector = $this->getDoctrine()
                ->getRepository('AppBundle:lector')
                ->findOneBy(['lector' => $id]);


            return $this->render('estacion_trabajo/ver_datos_cpuchasis.html.twig', ['datos' => $cpu, 'id' => $id, 'fuente' => $fuente, 'board' => $board, 'micro' => $micro,
                'ram' => $ram, 'hdd' => $hdd, 'mouse' => $mouse, 'teclado' => $teclado, 'bocina' => $bocina, 'tarjeta_video' => $tarjetaV, 'scanner' => $scanner, 'lector' => $lector, 'historial' => $incidencias]);
        }


        return $this->render('estacion_trabajo/editar_datos_cpuchasis.html.twig', ['form' => $form->createView(), 'id' => $id, 'tipo' => 'cpuchasis', 'chasis' => $cpu
        ]);

    }


    /**
     * @Route("estacion/{id}/{tipo}/edit", name="chasis_edit")
     */
    public function editChasisAction(Request $request, $id, $tipo)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $incidencia = $entityManager->getRepository(cpuchasis::class)->find($id);


        $incidencianForm = $this->createForm('AppBundle\Form\chasisFormType', $incidencia);
        /**
         *Star "Post only" section
         */
        $incidencianForm->handleRequest($request);
        if ($incidencianForm->isSubmitted() && $incidencianForm->isValid()) {


            $incidencia = $incidencianForm->getData();


            dump($incidencia);
            die();

            $entityManager->persist($incidencia);
            $entityManager->flush();
            $this->addFlash('success', 'Chasis Editado Correctamente');


            $datos = $this->getDoctrine()
                ->getRepository('AppBundle:cpuchasis')
                ->find($id);
            $incidencias = $this->getDoctrine()->getRepository('AppBundle:incidencia')->findBy(['inventario' => $id]);


            $fuente = $this->getDoctrine()
                ->getRepository('AppBundle:fuente')
                ->findOneBy(['fuentes' => $id]);


            $board = $this->getDoctrine()
                ->getRepository('AppBundle:motherboard')
                ->findOneBy(['motherboard' => $id]);

            $micro = $this->getDoctrine()
                ->getRepository('AppBundle:microprocesador')
                ->findOneBy(['microp' => $id]);

            $ram = $this->getDoctrine()
                ->getRepository('AppBundle:ram')
                ->findOneBy(['ram' => $id]);

            $hdd = $this->getDoctrine()
                ->getRepository('AppBundle:hdd')
                ->findOneBy(['hdd' => $id]);

            $mouse = $this->getDoctrine()
                ->getRepository('AppBundle:mouse')
                ->findOneBy(['mouse' => $id]);

            $teclado = $this->getDoctrine()
                ->getRepository('AppBundle:teclado')
                ->findOneBy(['teclado' => $id]);

            $bocina = $this->getDoctrine()
                ->getRepository('AppBundle:bocinas')
                ->findOneBy(['bocinas' => $id]);

            $tarjetaV = $this->getDoctrine()
                ->getRepository('AppBundle:tarjeta_video')
                ->findOneBy(['tarjetaV' => $id]);

            $scanner = $this->getDoctrine()
                ->getRepository('AppBundle:scanner')
                ->findOneBy(['scanner' => $id]);

            $lector = $this->getDoctrine()
                ->getRepository('AppBundle:lector')
                ->findOneBy(['lector' => $id]);

            // dump($tarjetaV);die();
            return $this->render('estacion_trabajo/ver_datos_cpuchasis.html.twig', ['datos' => $datos, 'id' => $id, 'fuente' => $fuente, 'board' => $board, 'micro' => $micro,
                'ram' => $ram, 'hdd' => $hdd, 'mouse' => $mouse, 'teclado' => $teclado, 'bocina1' => $bocina, 'tarjeta_video' => $tarjetaV, 'scanner' => $scanner, 'lector' => $lector, 'historial' => $incidencias]);
        }
        /**
         * End "Post only" section
         */
        $incidencianForm->add('Guardar', SubmitType::class, array('label' => 'Editar chasis'));
        return $this->render('estacion_trabajo/editar_datos_cpuchasis.html.twig', ['form' => $incidencianForm->createView(), 'tipo' => $tipo]);


    }

    /**
     * @Route("/estacion/ver_datos_estacion{id}",name="ver_datos_estacion2")
     */
    public function show_DatosEstacionAction($id, Request $request)
    {
        $compo = $request->get('componente');

        //dump($compo);
        // die();

        $datos = $this->getDoctrine()
            ->getRepository('AppBundle:inventario')
            ->find($id);

        if (!$datos) {
            throw $this->createNotFoundException(
                'No existe ningun plan guardado con este id' . $id
            );
        }
        // dump($this->tipo);
        // die();
        return $this->render('estacion_trabajo/ver_datos_estacion.html.twig', ['datos' => $datos, 'id' => $id, 'tipo' => $compo]);
    }


    /**
     * @Route("estacion/{id}/ver_datos",name="datos_estacion")
     */

    public function resp52Action(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $inventario_id = $entityManager->getRepository(inventario::class)->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($inventario_id);
//        dump($inventario[0]->getComponente()[0] );
//        die();
        $listaE = $inventario[0]->getEquipos();
        //dump($inventario);die();
//        foreach ($inventario[0]->getEquiposEnInventario() as $r) {
//            dump($r);
//        }
//        dump(sizeof($inventario[0]->getEquipos()));
//        die();
        $chasis = null;
        $fuente = null;
        $mother = null;
        $lector = new ArrayCollection();
        $teclado = null;
        $bocina = null;
        $micro = null;
        $mouse = null;
        $backup = null;
        $impresoras = new ArrayCollection();
        $monitores = new ArrayCollection();
        $scanner = null;
        $estabilizador = null;
        $ram = new ArrayCollection();
        $discosD = new ArrayCollection();
        // dump($listaE);die();
        foreach ($listaE as $e) {

            switch ($e->getTipoEquipo()) {
                case 'cpuchasis':
                    $chasis = $e;
//          dump($e);


                    break;
//                case 'fuente':
//                    $fuente = $e;
//                    break;
//                case 'motherboard':
//                    $mother = $e;
//                    break;
//                case 'lector':
//                    $lector = $e;
//                    break;
//                case 'teclado':
//                    $teclado = $e;
//                    break;
//                case 'bocina':
//                    $bocina = $e;
//                    break;
//                case 'micro':
//                    $micro = $e;
//                    break;
//                case 'mouse':
//                    $mouse = $e;
//                    break;
                case 'backup':
                    $backup = $e;
                    break;
                case 'impresora':
                    $impresoras->add($e);
                    break;
                case 'scanner':
                    $scanner = $e;
                    break;
                case 'estabilizador':
                    $estabilizador = $e;
                    break;
                case 'monitor':
                    $monitores->add($e);
                    break;
            }
        }
        foreach ($inventario[0]->getComponente() as $c) {
//                        dump($chasis->getComponente());die();
            switch ($c->getTipoComponente()) {
                case 'fuente':
                    $fuente = $c;
                    break;
                case 'motherboard':
                    $mother = $c;
                    break;
                case 'mouse':
                    $mouse = $c;
                    break;
                case 'lector':
                    $lector->add($c);
                    break;
                case 'teclado':
                    $teclado = $c;
                    break;
                case 'bocina':
                    $bocina = $c;
                    break;
                case 'microprocesador':
                    $micro = $c;
                    break;
                case 'ram':
                    $ram->add($c);
                    break;
                case 'hdd':
                    $discosD->add($c);
                    break;
            }

        }
        $usuarios = $this->getDoctrine()->getRepository('AppBundle:Administracion\Usuario')->findAllActive();
        $departamentos = $this->getDoctrine()->getRepository('AppBundle:departamento')->findAll();
//dump($inventario[0]->getEquipos());die();
        $incidencias = $entityManager->getRepository(incidencia::class)->findBy(['inventario' => $id]);
        return $this->render('estacion_trabajo/datos_estacion.html.twig', ['inventario' => $inventario,'departamentos'=>$departamentos,'usuarios'=>$usuarios, 'historial' => $incidencias,
            'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $discosD, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresoras' => $impresoras, 'monitores' => $monitores, 'scanner' => $scanner, 'estabilizador' => $estabilizador,'componentes'=>$inventario[0]->getComponente()]);

    }
//    /**
//     * A form having a theme and containing several fields
//     *
//     * @Route(
//     *      "/nuevoEquipoForm",
//     *      name = "nuevoEquipoForm"
//     * )
//     * @Template()
//     */
//    public function formHavingSeveralFieldsAction(Request $request)
//    {
//        $address = new Address();
//        $address->setName('Mickael Steller');
//        $address->setCompany('fuz.org');
//        $address->setStreet('41 rue de la Paix');
//        $address->setPostalcode('75002');
//        $address->setCity('Paris');
//        $address->setCountry('France');
//
//        $addresses = new Addresses();
//        $addresses->getAddresses()->add($address);
//
//        $form = $this->createForm(equipoFomType::class, $addresses);
//        if ($request->isMethod('POST')) {
//            $form->handleRequest($request);
//        }
//
//        return [
//            'form' => $form->createView(),
//            'data' => $addresses,
//        ];
//    }
    /**
     * @Route("estacion/{id}/ver_datos_inventario",name="datos_inventario_estacion")
     */

    public function datosInventarioEstacionAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $inventario_id = $entityManager->getRepository(inventario::class)->find($id);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($inventario_id);
        $listaE = $inventario[0]->getEquiposEnInventario();
        $chasis = null;
        $fuente = null;
        $mother = null;
        $lector = null;
        $teclado = null;
        $bocina = null;
        $micro = null;
        $mouse = null;
        $backup = null;
        $impresoras = new ArrayCollection();
        $monitores = new ArrayCollection();
        $scanner = null;
        $estabilizador = null;
        $ram = null;
        $hdd = null;
//    foreach ($inventario[0]->getComponenteInventario() as $i){
//        dump($i);
//    }
//dump($inventario[0]->getComponenteInventario());die();
        foreach ($listaE as $e) {

            switch ($e->getTipoEquipo()) {
                case 'cpuchasis':
                    $chasis = $e;
                    break;
//
                case 'backup':
                    $backup = $e;
                    break;
                case 'impresora':
                    $impresoras->add($e);
                    break;
                case 'scan':
                    $scanner = $e;
                    break;
                case 'estabilizador':
                    $estabilizador = $e;
                    break;
                case 'monitor':
                    $monitores->add($e);
                    break;
                case 'ram':
                    $ram = $e;
                    break;
                case 'hdd':
                    $hdd = $e;
                    break;
            }
        }
        foreach ($inventario[0]->getComponenteInventario() as $c) {
            switch ($c->getTipoComponente()) {
                case 'fuente':
                    $fuente = $c;
                    break;
                case 'motherboard':
                    $mother = $c;
                    break;
                case 'mouse':
                    $mouse = $c;
                    break;
                case 'lector':
                    $lector = $c;
                    break;
                case 'teclado':
                    $teclado = $c;
                    break;
                case 'bocina':
                    $bocina = $c;
                    break;
                case 'microprocesador':
                    $micro = $c;
                    break;
                case 'ram':
                    $ram = $c;
                    break;
                case 'hdd':
                    $hdd = $c;
                    break;
            }
        }

        // dump($hdd);die();
        $incidencias = $entityManager->getRepository(incidencia::class)->findBy(['inventario' => $id]);
        return $this->render('estacion_trabajo/datos_estacion.html.twig', ['inventario' => $inventario, 'historial' => $incidencias,
            'bocina' => $bocina, 'chasis' => $chasis, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresoras' => $impresoras, 'monitores' => $monitores, 'scanner' => $scanner, 'estabilizador' => $estabilizador]);

    }


    /**
     * @Route("/estacion/ver_nombre_estacion{idestacion}",name="ver_nombre_estacion")
     */
    public function show_NombreEstacion($id, Request $request)
    {
        $compo = $request->get('componente');

        //dump($compo);
        // die();

        $datos = $this->getDoctrine()
            ->getRepository('AppBundle:estacion')
            ->find($id);

        if (!$datos) {
            throw $this->createNotFoundException(
                'No existe ningun plan guardado con este id' . $id
            );
        }
        // dump($this->tipo);
        // die();
        return $this->render('estacion_trabajo/ver_datos_periferico.html.twig', ['datos' => $datos, 'id' => $id, 'tipo' => $compo]);
    }

    /**
     * @Route("/reportes/estacion/filter/",name="equipo_filter")
     * @param Request $request
     *
     * @return Response
     */
    public function filterEquipoAction(Request $request)
    {
        //$p = $this->get('knp_paginator')->paginate();
        $entityManager = $this->getDoctrine()->getManager();
//        dump($request);
//        die();
        $ordenar = null;
        $direccion = null;
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

        $area = $request->request->get('usuarios')[0];
        $dep = $request->request->get('costos')[0];
        $estacion = $request->request->get('estaciones')[0];
        $numInv = $request->request->get('tipo');
        //  $p=$request->request->get('pagination');
        // dump($dep);die();
        $entityManager = $this->getDoctrine()->getManager();
        //$select = $request->request->get('componente');

        if ($dep == '' && $estacion == '' && $numInv == '') {
            $this->addFlash('alerta', 'Usted debe llenar los filtros para buscar');
        }


        if ($dep != '' && $estacion != '') {
            //$applicationRepository = $entityManager->getRepository('AppBundle:inventario');
            //$inventario=$applicationRepository->TodoCentroCosto($this->tipo,$numInv);
            $inventario = $entityManager->getRepository('AppBundle:inventario')->find($estacion);

            /*  $repository = $this->getDoctrine()
          ->getRepository('AppBundle:' . $this->tipo);

  */
            /* $query = $repository->createQueryBuilder('tabla')
         ->where('tabla.numInventario = :numI')
         ->setParameter('numI', $numInv)
         ->orderBy('tabla.id', 'DESC')
         ->setMaxResults(1)
         ->getQuery()->getResult();
       $products = $query;*/
            //dump($products);
            //   die();
            $em = $this->get('doctrine.orm.entity_manager');
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $query = $repository->createQueryBuilder('tabla')
                ->where('tabla.estacion = :estacion')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('estacion', $inventario)
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery()->getResult();
            //$query = $em->createQuery($dql);
            // $a = $query->execute();
//            dump($query);
//            die();
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );

            $p = $pagination;


            return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                "areas" => $area,
                'lista' => $query,
                'orden' => $ordenar,
                'dir' => $direccion,
                'componente' => $this->tipo,
                "pagination" => $pagination,
            ]);

        }


        $this->addFlash('alert', 'No existe equipo con los datos seleccionados');
        return $this->redirectToRoute('lista_equipos');
    }

    /**
     * @Route("/reportes/estacion/componente_filter",name="componente_filter")
     * @param Request $request
     *
     * @return Response
     */
    public function filterComponenteAction(Request $request)
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

        $area = $request->request->get('usuarios')[0];
        $dep = $request->request->get('costos')[0];
        $estacion = $request->request->get('estaciones')[0];
        $numInv = $request->request->get('tipo');
        //  $p=$request->request->get('pagination');
        // dump($dep);die();
        $entityManager = $this->getDoctrine()->getManager();
        //$select = $request->request->get('componente');

        if ($dep == '' && $estacion == '' && $numInv == '') {
            $this->addFlash('alerta', 'Usted debe llenar los filtros para buscar');
        }


        if ($dep != '' && $estacion != '') {
            //$applicationRepository = $entityManager->getRepository('AppBundle:inventario');
            //$inventario=$applicationRepository->TodoCentroCosto($this->tipo,$numInv);
            $inventario = $entityManager->getRepository('AppBundle:inventario')->find($estacion);

            /*  $repository = $this->getDoctrine()
          ->getRepository('AppBundle:' . $this->tipo);

      */
            /* $query = $repository->createQueryBuilder('tabla')
         ->where('tabla.numInventario = :numI')
         ->setParameter('numI', $numInv)
         ->orderBy('tabla.id', 'DESC')
         ->setMaxResults(1)
         ->getQuery()->getResult();
       $products = $query;*/
            //dump($products);
            //   die();
            $em = $this->get('doctrine.orm.entity_manager');
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:equipo');
            $chasis = $repository->createQueryBuilder('tabla')
                ->where('tabla.estacion = :estacion')
                // ->andWhere('tabla.inventario =: idE')
                ->setParameter('estacion', $inventario)
                ->andWhere('tabla.tipoEquipo =:tipo')
                ->setParameter('tipo', 'cpuchasis')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery()->getResult();
            //$query = $em->createQuery($dql);
            // $a = $query->execute();
            if ($chasis != null) {
                $repository = $this->getDoctrine()
                    ->getRepository('AppBundle:componente');
                $query = $repository->createQueryBuilder('tabla')
                    ->where('tabla.estacion2 = :inventario')
                    // ->andWhere('tabla.inventario =: idE')
                    ->setParameter('inventario',$inventario)
                    // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                    ->getQuery()->getResult();
//            dump($query);
//            die();
                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $query,
                    $request->query->getInt('page', 1),
                    10
                );

                $p = $pagination;

                return $this->render('estacion_trabajo/lista_componentes.html.twig', [
                    "areas" => $area,
                    'lista' => $query,
                    'componente' => $this->tipo,
                    "pagination" => $pagination,
                ]);
            } else {
                $this->addFlash('alert', 'No existe chasis en esta estacion');
                return $this->redirectToRoute('lista_componentes');
            }

        }


        $this->addFlash('alert', 'No existe chasis en esta estacion');
        return $this->redirectToRoute('lista_componentes');
    }


    /**
     * @Route("/reportes/equipo/list/{tipo}/{dep}/{estacion}/{numInv}/{select}", name="equipoLista")
     * @Method("POST")
     * @return Response
     */
    public function listEAction($tipo, $area, $dep, $estacion, $numInv, $select, Request $request)
    {


        $this->tipo = $tipo;

        $componente_seleccionado = $this->tipo;
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

        //$applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        //$lista_temporal = $applicationRepository->findAll();
        // $paginator = $this->get('knp_paginator');


        if (empty($this->tipo) != true) {
            // dump($this->tipo);
            //die();
            //$componente_seleccionado = $this->tipo;

            //Consulta para filtrar por tipo de periferico y numero de inventario
            if ($dep == '' && $estacion == '' && $numInv != '') {
                //$applicationRepository = $entityManager->getRepository('AppBundle:inventario');
                //$inventario=$applicationRepository->TodoCentroCosto($this->tipo,$numInv);


                $repository = $this->getDoctrine()
                    ->getRepository('AppBundle:' . $this->tipo);


                $query = $repository->createQueryBuilder('tabla')
                    ->where('tabla.numInventario = :numI')
                    ->setParameter('numI', $numInv)
                    ->getQuery();
                $products = $query->getResult();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $products,
                    $request->query->getInt('page', 1),
                    10
                );

                $this->pagination = $pagination;
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                    "areas" => $area,
                    'lista' => $products,
                    'componente' => $this->tipo,
                    "pagination" => $pagination,
                ]);

            }

//Consulta para filtrar x estacion de trabajo y tipo de periferico
            if ($dep != '' && $estacion != '' && $numInv == '') {
                //$applicationRepository = $entityManager->getRepository('AppBundle:inventario');
                //$inventario=$applicationRepository->TodoCentroCosto($this->tipo,$numInv);

                $id_estacionT = $entityManager->getRepository('AppBundle:inventario')->find($estacion[0])->getId();

                $repository = $this->getDoctrine()
                    ->getRepository('AppBundle:' . $this->tipo);


                $query = $repository->createQueryBuilder('tabla')
                    ->where('tabla.inventario = :ide')
                    // ->andWhere('tabla.inventario =: idE')
                    ->setParameter('ide', $id_estacionT)
                    // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                    ->getQuery();
                $products = $query->getResult();
                //  dump($products);
                // die();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $products,
                    //$request->query->getInt('page', 1),
                    10
                );

                $this->pagination = $pagination;
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                    "areas" => $area,
                    'lista' => $products,
                    'componente' => $this->tipo,
                    "pagination" => $pagination,
                ]);

            }

            /* if ($dep == '' && $estacion == '' && $numInv == ''){

         $repository = $this->getDoctrine()
           ->getRepository('AppBundle:' . $this->tipo);

         $incidencias = $repository->findAll();

         $paginator = $this->get('knp_paginator');
         $pagination = $paginator->paginate(
           $incidencias,
           //$request->query->getInt('page', 1),
           10
         );

         return $this->render('estacion_trabajo/lista_equipos.html.twig', [
           "filters" => $this->filters,
           "areas" => $area,
           'lista' => $incidencias,
           'componente' => $this->tipo,
           "pagination" => $pagination,
         ]);
       }
 */

            if ($this->tipo == 'backup' && $dep == '' && $estacion == '' && $numInv == '') {

                $applicationRepository = $entityManager->getRepository('AppBundle:backup');

                $incidencias = $applicationRepository->findAll();

                //dump($incidencias);
                //  die();
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                //  dump($incidencias[0]);
                //die();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    $request->query->getInt('page', 1),
                    10
                );

                $this->pagination = $pagination;
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                    "filters" => $this->filters,
                    "areas" => $area,
                    'lista' => $incidencias,
                    'componente' => $this->tipo,
                    "pagination" => $pagination,
                ]);

            } else if ($this->tipo == 'estabilizador' && $dep == '' && $estacion == '' && $numInv == '') {

                $applicationRepository2 = $entityManager->getRepository('AppBundle:estabilizador');
                $incidencias = $applicationRepository2->findAll();


                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                    "filters" => $this->filters,
                    "areas" => $area,
                    'lista' => $incidencias,
                    "pagination" => $pagination,
                    'componente' => $this->tipo,
                ]);
            } else if ($this->tipo == 'cpuchasis' && $dep == '' && $estacion == '' && $numInv == '') {

                $applicationRepository = $entityManager->getRepository('AppBundle:cpuchasis');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );

                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]

                );

            } else if ($this->tipo == 'impresora' && $dep == '' && $estacion == '' && $numInv == '') {

                $applicationRepository = $entityManager->getRepository('AppBundle:impresora');

                $incidencias = $applicationRepository->findAll();


                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]

                );

            } else if ($this->tipo == 'monitor' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:monitor');

                $incidencias = $applicationRepository->findAll();


                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'bocina' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:bocinas');

                $incidencias = $applicationRepository->findAll();


                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;

//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'fuente' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:fuente');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'hdd' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:hdd');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'lector' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:lector');

                $incidencias = $applicationRepository->findAll();


                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
                // dump($this->$pagination);
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'board' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:motherboard');

                $incidencias = $applicationRepository->findAll();


                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'micro' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:microprocesador');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'mouse' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:mouse');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'ram' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:ram');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    //$request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            } else if ($this->tipo == 'teclado' && $dep == '' && $estacion == '' && $numInv == '') {

                //$this->nombreE=$nombreEstacion;

                $applicationRepository = $entityManager->getRepository('AppBundle:teclado');

                $incidencias = $applicationRepository->findAll();

                $paginator = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $incidencias,
                    $request->query->getInt('page', 1),
                    10
                );
                $this->pagination = $pagination;
//     return $this->redirect('estacion_trabajo/adicionar_componentes.html.twig',
                return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                        "filters" => $this->filters,
                        "areas" => $area,
                        'lista' => $incidencias,
                        "pagination" => $pagination,
                        'componente' => $this->tipo,
                    ]
                );

            }
        } else
            return $this->render('estacion_trabajo/lista_equipos.html.twig', [
                "filters" => $this->filters,

                'incidencias' => '',
                'lista' => '',
                'componente' => $this->tipo,
                "pagination" => $this->pagination,
                "areas" => $area]);
        /* return $this->render(
       'estacion_trabajo/nueva_estacion.html.twig',
       [
         //  'componente' => $componente_seleccionado,
         "filters" => $this->filters,
         "pagination" => $this->pagination,
        // 'estacionForm'=>$estacionForm,
       ]
     );*/
    }

    /**
     * @Route("reportes/lista_equipos", name="lista_equipos")
     */
    public function listaEquiposAction(Request $request, $maxItemPerPage = 10)
    {
        // dump($request);die();
        $entityManager = $this->getDoctrine()->getManager();
        // $select = $request->request->get('componente');
        // dump($select);
        // die();
        $accesorio = '';
        //  $accesorio = $this->getDoctrine()->getRepository('AppBundle:' . $select)->findAll();

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


        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:equipo');
        // $equipos = $applicationRepository->findAll();
      //  $dql = "SELECT e.tipoEquipo,e.departamento,e.inventario,e.numI,e.marca,e.estado FROM AppBundle:equipo e where e.estado!=0 ";
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');
        $query = $repository->createQueryBuilder('e')
            ->where('e.estado !=0')
//            ->setParameter('area', $dep)
////            ->andWhere('tabla.user=:usuario_actual')
////            ->setParameter('usuario_actual', $this->getUser()->getUsername())
            ->orderBy('e.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
       // $lista = $entityManager->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

//dump($lista->execute());die();
        return $this->render(
            'estacion_trabajo/lista_equipos.html.twig', array('pagination' => $pagination, 'inventarios' => '',
                "filters" => $this->filters, 'areas' => $area, 'orden' => $ordenar,
                'componente' => 'backup',
                'inventarios' => $accesorio,
                'lista' => '',
                'orden' => $ordenar,
                'dir' => $direccion,

            )
        );
    }


    /**
     * @Route("reportes/lista_componentes", name="lista_componentes")
     */
    public function listaComponentesAction(Request $request)
    {
        //dump($request);die();
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


        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:componente');
        $componentes = $applicationRepository->findAll();


        return $this->render(
            'estacion_trabajo/lista_componentes.html.twig', [
                "filters" => $this->filters,
                'areas' => $area,
                'componente' => '',
                'inventarios' => $accesorio,
                'lista' => $componentes,

            ]

        );
    }

    /**
     * Matches /blog exactly
     *
     * @Route("estacion/adicionar_a_inventario/{tipo}/{idperiferico}/{idestacion}", name="adicionar_a_inventario")
     * @return ArrayCollection
     */
    public function guardar_en_InventarioAction($tipo, $idperiferico, $idestacion)
    {
        //  dump($tipo);
        // die();

        $entityManager = $this->getDoctrine()->getManager();
        $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($idperiferico);

        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');

        $existe = $applicationRepository->findOneBy(['idperiferico' => $idperiferico]);
        $existe_tipo = $applicationRepository->findOneBy(['tipo' => $tipo]);

        $applicationRepository1 = $entityManager->getRepository('AppBundle:inventario');
        $estacion = $applicationRepository1->find($idestacion);

        // dump($estacion);die();
        $nombreEstacion = $estacion->getNombreRed();

        $cantidad_monitor = $applicationRepository->findBy(['tipo' => 'monitor']);

        // dump($nombreEstacion);
        // die();
        $this->nombreE = $nombreEstacion;
        //dump( $this->nombreE);
        // die();

        $equipos_en_temporal = $applicationRepository->findAll();

//    dump( $accesorio);
//     die();
        if ($accesorio->getTipoEquipo() != 'impresora' && $accesorio->getTipoEquipo() != 'monitor' && $existe_tipo) {

            $this->addFlash('advertencia', 'Este inventario ya contiene un ' . $tipo);
            $lista_temporal = $applicationRepository->findAll();
            return $this->render('estacion_trabajo/adicionar_componentes.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
                "pagination" => $this->pagination]);

        } elseif ($applicationRepository->findBy(['inventario' => $accesorio->getNumInventario()])) {
            $this->addFlash('advertencia', 'Este equipo ya esta adicionado en el inventario');
            $lista_temporal = $applicationRepository->findAll();
//            dump('ahora aqui');
            return $this->render('estacion_trabajo/adicionar_componentes.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
                "pagination" => $this->pagination]);
        } elseif ($accesorio->getEstacion() != null) {
            $this->addFlash('advertencia', 'Este equipo ya esta asignado a una estacion');
        } else {
//            dump('ahora si');
            $temporal1 = new temporal();
            $temporal1->setTipo($tipo);
            $temporal1->setCantidad('1');
            $temporal1->setInventario($accesorio->getNumInventario());
            $temporal1->setMarca($accesorio->getMarca());
            $temporal1->setIdperiferico($idperiferico);


            $entityManager->persist($temporal1);

            $entityManager->flush();
        }


        $lista_temporal = $applicationRepository->findAll();


        return $this->render('estacion_trabajo/adicionar_componentes.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
            "pagination" => $this->pagination]);

    }

    /**
     * Matches /blog exactly
     *
     * @Route("estacion/adicionar_a_estacion/{numI}/{idInventario}", name="adicionar_a_Estacion")
     */
    public function guardar_en_Inventario2Action($numI, $idInventario)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);

        $inventario = $entityManager->getRepository('AppBundle:inventario')->find($idInventario);
        $inventario->addEquipo($accesorio[0]);
        $accesorio[0]->setEstacion($inventario);
        $entityManager->persist($accesorio[0]);
        $entityManager->flush();

        return $this->redirectToRoute('editar_inventario', ['id' => $inventario->getId(), 'nuevo' => '']);

    }

    /**
     * @Route("estacion/nuevos_equipos/{numI}",name="lista_temporalEquipos")
     *
     */
    public function listaTemporalEquipos(Request $request, $numI)
    {
        $listaParaAgregar = new ArrayCollection();
        $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);
        $listaParaAgregar->add($accesorio[0]);
        $responseArray = array();
        //  dump($accesorio);die();
        if (is_iterable($listaParaAgregar)) {
            foreach ($listaParaAgregar as $r) {
                $responseArray[] = array(
                    "numI" => $r->getNumInventario(),
                    "tipoEquipo" => $r->getTipoEquipo()
                );
            }
        }
        // $this->redirectToRoute('nueva_estacion')
        //   return $this->render('estacion_trabajo/nueva_estacion.html.twig');
        return new JsonResponse($responseArray);
        //    dump($numI);die();
    }

    /**
     * Matches /blog exactly
     *
     * @Route("estacion/adicionar_a_inventarioNI2/{tipo}/{idperiferico}/{idestacion}/{incidencia_id}", name="adicionar_a_inventarioNInc")
     * @return ArrayCollection
     */
    public function guardar_en_InventarioNIAction($tipo, $idperiferico, $idestacion, $incidencia_id)
    {
//        dump($tipo);
//        die();

        $entityManager = $this->getDoctrine()->getManager();
        $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($idperiferico);

        // $applicationRepository = $entityManager->getRepository('AppBundle:temporal');

        $lista_temporal = new ArrayCollection();
        $existe_tipo = false;
        $cantidad_monitor = 0;
        if (!$lista_temporal->isEmpty()) {
            foreach ($lista_temporal as $l) {
                if ($l->getTipo() == $tipo) {
                    $existe_tipo = true;
                } elseif ($l->getTipo == 'monitor') {
                    $cantidad_monitor = $cantidad_monitor + 1;
                }
            }
        }


        //$existe = $applicationRepository->findOneBy(['idperiferico' => $idperiferico]);
        $existe_tipo = $applicationRepository->findOneBy(['tipo' => $tipo]);

        $applicationRepository1 = $entityManager->getRepository('AppBundle:inventario');
        $estacion = $applicationRepository1->find($idestacion);

        // dump($estacion);die();
        $nombreEstacion = $estacion->getNombreRed();

        //  $cantidad_monitor = $applicationRepository->findBy(['tipo' => 'monitor']);

        // dump($nombreEstacion);
        // die();
        $this->nombreE = $nombreEstacion;
        //dump( $this->nombreE);
        // die();

        // $equipos_en_temporal = $applicationRepository->findAll();
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($incidencia_id);
        $equipos_en_estacion = $entityManager->getRepository('AppBundle:equipo')->findBy(['estacion' => $estacion]);
        if ($existe_tipo && ($accesorio->getTipoEquipo() != 'impresora' || $accesorio->getTipoEquipo() != 'monitor')) {
            $this->addFlash('advertencia', 'Este inventario ya contiene un ' . $tipo);
            $lista_temporal = $l;
            return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
                "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);
        } elseif ($applicationRepository->findBy(['inventario' => $accesorio->getNumInventario()])) {
            $this->addFlash('advertencia', 'Este equipo ya esta adicionado en el inventario');
            $lista_temporal = $applicationRepository->findAll();
            return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
                "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);
        } elseif ($accesorio->getEstacion() != null) {
            $this->addFlash('advertencia', 'Este equipo ya esta asignado a una estacion');
        } else {
            $temporal1 = new temporal();
            $temporal1->setTipo($tipo);
            $temporal1->setCantidad('1');
            $temporal1->setInventario($accesorio->getNumInventario());
            $temporal1->setMarca($accesorio->getMarca());
            $temporal1->setIdperiferico($idperiferico);


            $entityManager->persist($temporal1);

            $entityManager->flush();
        }


        $lista_temporal = $applicationRepository->findAll();


        return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
            "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);

    }

    /**
     * Matches /blog exactly
     *
     * @Route("estacion/annadirAInventario", name="annadirAInv")
     *
     */
    public function adicionAInventarioAction(Request $request)
    {
        $componentes = [];
        if ($request->query->get('piezas') != null) {
            $componentes = $request->query->get('piezas');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $nombreEstacion = $request->query->get('nombre_estacion');
        $asuntoIncidencia = $request->query->get('asunto');
        $resumen = $request->query->get('resumen');
        $estacion = $entityManager->getRepository('AppBundle:inventario')->findBy(['nombreRed' => $nombreEstacion])[0];
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');
        $query = $repository->createQueryBuilder('tabla')
            ->where('tabla.estacion = :est')
            // ->andWhere('tabla.inventario =: idE')
            ->setParameter('est', $estacion)
            ->andWhere('tabla.tipoEquipo = :asesorio')
            ->setParameter('asesorio', 'cpuchasis')
            ->getQuery();
        $chasis = $query->execute();
        $estacionForm = $this->createForm('AppBundle\Form\EstacionForm', $estacion);
        // dump($estacionForm);die();
//    $chasisForm = $this->createForm('AppBundle\Form\equipoFomType', $chasis[0]);
        //  $chasisForm->add('Guardar', SubmitType::class, array('label' => 'Guardar cambios'));
        if ($estacionForm->isSubmitted() && $estacionForm->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            dump($request);
            die();
//            $plan = $form->getData();
//            $plan->setEstado('Activo');
//            $plan->setTipoEquipo($tipo);
//            $entityManager = $this->getDoctrine()->getManager();
//            $entityManager->persist($plan);
//
//            $entityManager->flush();
//            $this->addFlash('success', 'Los datos han sido insertados correctamente');
//            return $this->redirectToRoute('lista_equipos');
        }
//        dump($chasisForm);
//        die();

//        dump($request);
//        die();
//        $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($idperiferico);
//
//        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
//
//        $lista_temporal=new ArrayCollection();
//        $existe_tipo=false;
//        $cantidad_monitor=0;
//        if(!$lista_temporal->isEmpty()){
//            foreach ($lista_temporal as $l){
//                if($l->getTipo()==$tipo){
//                    $existe_tipo=true;
//                }
//                elseif ($l->getTipo=='monitor'){
//                    $cantidad_monitor=$cantidad_monitor+1;
//                }
//            }
//        }
//
//
//        //$existe = $applicationRepository->findOneBy(['idperiferico' => $idperiferico]);
//      //  $existe_tipo = $applicationRepository->findOneBy(['tipo' => $tipo]);

//        $applicationRepository1 = $entityManager->getRepository('AppBundle:inventario');
//        $estacion = $applicationRepository1->find($idestacion);
//
//        // dump($estacion);die();
//        $nombreEstacion = $estacion->getNombreRed();
//
//        //  $cantidad_monitor = $applicationRepository->findBy(['tipo' => 'monitor']);
//
//        // dump($nombreEstacion);
//        // die();
//        $this->nombreE = $nombreEstacion;
//        //dump( $this->nombreE);
//        // die();
//
//        // $equipos_en_temporal = $applicationRepository->findAll();
//        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($incidencia_id);
//        $equipos_en_estacion = $entityManager->getRepository('AppBundle:equipo')->findBy(['estacion' => $estacion]);
//        if ($existe_tipo && ($accesorio->getTipoEquipo() != 'impresora' || $accesorio->getTipoEquipo() != 'monitor')) {
//            $this->addFlash('advertencia', 'Este inventario ya contiene un ' . $tipo);
//            $lista_temporal = $l;
//            return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
//                "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);
//        } elseif ($applicationRepository->findBy(['inventario' => $accesorio->getNumInventario()])) {
//            $this->addFlash('advertencia', 'Este equipo ya esta adicionado en el inventario');
//            $lista_temporal = $applicationRepository->findAll();
//            return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
//                "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);
//        } elseif ($accesorio->getEstacion() != null) {
//            $this->addFlash('advertencia', 'Este equipo ya esta asignado a una estacion');
//        } else {
//            $temporal1 = new temporal();
//            $temporal1->setTipo($tipo);
//            $temporal1->setCantidad('1');
//            $temporal1->setInventario($accesorio->getNumInventario());
//            $temporal1->setMarca($accesorio->getMarca());
//            $temporal1->setIdperiferico($idperiferico);
//
//
//            $entityManager->persist($temporal1);
//
//            $entityManager->flush();
//        }
//
//
//        $lista_temporal = $applicationRepository->findAll();


        return $this->render('estacion_trabajo/nuevosEquiposAInventario.html.twig', ['idestacion' => $estacion->getId(), 'elements' => $this->lista_componentes, 'nombre_estacion' => $estacion->getNombreRed(), 'lista' => null, "filters" => $this->filters, 'lista_componentes' => $estacion->getComponente(),
            "pagination" => $this->pagination, 'incidencia' => '', 'lista_equipos' => $estacion->getEquipos(), 'componentes' => $componentes, 'form' => $estacionForm->createView()]);

    }


    /**
     * Matches /blog exactly
     *
     * @Route("estacion/completar_estacion/{idestacion}/", name="completar_estacion")
     * @return Response
     */
    public function completarEstacionAction($idestacion)
    {
        //  dump($tipo);
        // die();

        $entityManager = $this->getDoctrine()->getManager();
//    $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($idperiferico);
//
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
//
//    $existe = $applicationRepository->findOneBy(['idperiferico' => $idperiferico]);
//    $existe_tipo = $applicationRepository->findOneBy(['tipo' => $tipo]);

        $applicationRepository1 = $entityManager->getRepository('AppBundle:inventario');
        $estacion = $applicationRepository1->find($idestacion);

        // dump($estacion);die();
        $nombreEstacion = $estacion->getNombreRed();

        // $cantidad_monitor = $applicationRepository->findBy(['tipo' => 'monitor']);

        // dump($nombreEstacion);
        // die();
        $this->nombreE = $nombreEstacion;
        //dump( $this->nombreE);
        // die();

//    $equipos_en_temporal = $applicationRepository->findAll();
//    $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($incidencia_id);
//    $equipos_en_estacion = $entityManager->getRepository('AppBundle:equipo')->findBy(['estacion' => $estacion]);
//    if ($existe_tipo && ($accesorio->getTipoEquipo() != 'impresora' || $accesorio->getTipoEquipo() != 'monitor')) {
//      $this->addFlash('advertencia', 'Este inventario ya contiene un ' . $tipo);
//      $lista_temporal = $applicationRepository->findAll();
//      return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
//        "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);
//    } elseif ($applicationRepository->findBy(['inventario' => $accesorio->getNumInventario()])) {
//      $this->addFlash('advertencia', 'Este equipo ya esta adicionado en el inventario');
//      $lista_temporal = $applicationRepository->findAll();
//      return $this->render('estacion_trabajo/adicionar_componentesNI.html.twig', ['idestacion' => $idestacion, 'elements' => $this->lista_componentes, 'nombre_estacion' => $this->nombreE, 'lista' => $lista_temporal, "filters" => $this->filters,
//        "pagination" => $this->pagination, 'incidencia' => $incidencia, 'lista_equipos' => $equipos_en_estacion]);
//    } elseif ($accesorio->getEstacion() != null) {
//      $this->addFlash('advertencia', 'Este equipo ya esta asignado a una estacion');
//    } else {
//      $temporal1 = new temporal();
//      $temporal1->setTipo($tipo);
//      $temporal1->setCantidad('1');
//      $temporal1->setInventario($accesorio->getNumInventario());
//      $temporal1->setMarca($accesorio->getMarca());
//      $temporal1->setIdperiferico($idperiferico);
//
//
//      $entityManager->persist($temporal1);
//
//      $entityManager->flush();
//    }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->delete('AppBundle:temporal', 't')
            ->getQuery();
        $query->execute();
        $lista_temporal = $applicationRepository->findAll();


        return $this->render('estacion_trabajo/completar_estacion.html.twig', ['estacion' => $estacion, 'lista' => $lista_temporal, "filters" => $this->filters,
            "pagination" => $this->pagination]);

    }


    /**
     * @Route("/reportes/estacion/nuevosEquiposAE",name="nuevosEquiposEstacion")
     * @param Request $request
     */
    public function nuevosEquiposEstacionAction(Request $request)
    {
        dump($request);
        die();
        $idEstacion = $request->query->get('idEst');
        $numInvEquipo = $request->query->get('numI');
        $entityManager = $this->getDoctrine()->getManager();
        $estacion = $entityManager->getRepository('AppBundle:inventario')->find($idEstacion);
        $equipo = $entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numInvEquipo]);

        if ($numInvEquipo != null && $equipo[0]->getEstacion() == null) {
            $estacion->addEquipo($equipo[0]);
            $equipo[0]->setEstacion($estacion);
            $entityManager->persist($estacion);
            $zonaHoraria = new \DateTimeZone('America/Cuiaba');
            $fecha_actual = new \DateTime('now', $zonaHoraria);
            $incidenciaInstalacion = new incidencia();
            $incidenciaInstalacion->setTipoMov('Instalacion de nuevo equipamiento informatico');
            $incidenciaInstalacion->setTipo('Instalacion de nuevo equipamiento informatico');
            $incidenciaInstalacion->setTipoMov('Instalacion');
            $incidenciaInstalacion->setAsesorio($equipo[0]->getTipoEquipo());
            $incidenciaInstalacion->setDpto($equipo[0]->getDepartamento());
            $incidenciaInstalacion->setNumInventario($equipo[0]->getNumInventario());
            $incidenciaInstalacion->setEstado('Solucionado');
            $incidenciaInstalacion->setEstadoMovimiento('');
            $incidenciaInstalacion->setTecAsignado($this->getUser());
            $incidenciaInstalacion->setUser($this->getUser()->getUsername());
            $incidenciaInstalacion->setInventario($estacion);
            $incidenciaInstalacion->setRespuesta('');
            $incidenciaInstalacion->setIdE($equipo[0]->getId());
            $incidenciaInstalacion->setFecha($fecha_actual);
            $incidenciaInstalacion->setFechaA($fecha_actual);
            $incidenciaInstalacion->setResumen($resumen);

            $entityManager->persist($equipo[0]);
            $entityManager->flush();

//        dump($estacion);
//        die();
            $responseArray[] = array(
                "numI" => $equipo[0]->getNumInventario(),
                "modelo" => $equipo[0]->getModelo(),
                "tipoEquipo" => $equipo[0]->getTipoEquipo()
            );
        }
        return new JsonResponse($responseArray);
    }

    /**
     * @Route("/reportes/estacion/salvar_inventario/{idestacion}",name="inventario_final")
     */
    public function inventarioVersionFinalAction($idestacion)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $applicationRepository2 = $entityManager->getRepository('AppBundle:inventario')->find($idestacion);
        $lista_temporal = $applicationRepository->findAll();
        $nombre_estacion = $applicationRepository2->getNombreRed();
        $equipo = '';
        foreach ($lista_temporal as $listaP) {
            $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($listaP->getIdperiferico());
            $applicationRepository2->addEquipo($accesorio);
            $accesorio->setEstacion($applicationRepository2);
            $entityManager->persist($accesorio);
        }
        $entityManager->persist($applicationRepository2);
//    dump($applicationRepository2);
//    die();
        $entityManager->flush();
        //dump($applicationRepository2->getEquipo());
        // die();
        $this->addFlash('mensaje', 'Inventario guardado correctamente');
        // dump($nombre_estacion);
        // die();
        // $this->limpiar_estacion($nombre_estacion, 'nuevo',$accesorio->getTipoEquipo(),);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($idestacion);
        // $lista_componentes = $accesorio->getComponente();
        //dump($inventario[0]->getEquipo());
        // die();
        $listaE = $inventario[0]->getEquipos();
        $chasis = null;
        $fuente = null;
        $mother = null;
        $lector = null;
        $teclado = null;
        $bocina = null;
        $micro = null;
        $mouse = null;
        $backup = null;
        $impresoras = new ArrayCollection();
        $monitores = new ArrayCollection();
        $ram = null;
        $hdd = null;

        foreach ($listaE as $e) {
            //dump($e);die();
            switch ($e->getTipoEquipo()) {
                case 'cpuchasis':
                    $chasis = $e;

                    foreach ($chasis->getComponente() as $c) {
                        switch ($c->getTipoComponente()) {
                            case 'fuente':
                                $fuente = $c;
                                break;
                            case 'motherboard':
                                $mother = $c;
                                break;
                            case 'mouse':
                                $mouse = $c;
                                break;
                            case 'lector':
                                $lector = $c;
                                break;
                            case 'teclado':
                                $teclado = $c;
                                break;
                            case 'bocina':
                                $bocina = $c;
                                break;
                            case 'microprocesador':
                                $micro = $c;
                                break;
                            case 'ram':
                                $ram = $c;
                                break;
                            case 'hdd':
                                $hdd = $c;
                                break;
                        }

                    }
                    break;
                case 'fuente':
                    $fuente = $e;
                    break;
                case 'motherboard':
                    $mother = $e;
                    break;
                case 'lector':
                    $lector = $e;
                    break;
                case 'teclado':
                    $teclado = $e;
                    break;
                case 'bocina':
                    $bocina = $e;
                    break;
                case 'micro':
                    $micro = $e;
                    break;
                case 'mouse':
                    $mouse = $e;
                    break;
                case 'backup':
                    $backup = $e;
                    break;
                case 'impresora':
                    $impresoras->add($e);
                    break;
                case 'monitor':
                    $monitores->add($e);
                    break;
                case 'ram':
                    $ram = $e;
                    break;
                case 'hdd':
                    $hdd = $e;
                    break;
            }
        }
//dump($chasis);die();

        return $this->render('estacion_trabajo/datos_estacion.html.twig', ['inventario' => $inventario, 'chasis' => $chasis, 'bocina' => $bocina, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresoras' => $impresoras, 'monitores' => $monitores]);
    }

    /**
     * @Route("/reportes/estacion/salvar_inventario_nuevo_incidencia/{idestacion}",name="inventario_final_nuevo")
     */
    public function crearNuevoInventarioAction($idestacion)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $inventario_actual = $entityManager->getRepository('AppBundle:inventario')->find($idestacion);
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->findBy(['inventario' => $idestacion]);
        $lista_temporal = $applicationRepository->findAll();
        $nombre_estacion = $inventario_actual->getNombreRed();
        $equipo = '';
        //Creacion del nuevo inventario,lo unico que cambia son los equipos dentro
//        $nuevo_inventario = new inventario();
//        $nuevo_inventario->setEstado('Activo');
//        $nuevo_inventario->setAntivirus($inventario_actual->getAntivirus());
//        $nuevo_inventario->setAntivirus('KASP');
//        $nuevo_inventario->setCentroCosto($inventario_actual->getCentroCosto());
//        // $nuevo_inventario->setEstado($inventario_actual->getEstado());
//        $nuevo_inventario->setFechacreacion(new \DateTime('now'));
//        $nuevo_inventario->setIp($inventario_actual->getIp());
//        $nuevo_inventario->setJefeInformatica($inventario_actual->getJefeInformatica());
//        $nuevo_inventario->setNombreRed($inventario_actual->getNombreRed());
//        $nuevo_inventario->setPassSetup($inventario_actual->getPassSetup());
//        $nuevo_inventario->setResponsable($inventario_actual->getResponsable());
//        $nuevo_inventario->setTecnico($this->getUser()->getUsername());
//        $inventario_actual->setActivo('No');
//        $inventario_actual->setEstado('Inactivo');

        // dump($nuevo_inventario);die();
//    $incidencia=new incidencia();
//    $incidencia->setInventario($nuevo_inventario);
//    $incidencia->setAsunto('Creacion de nuevo inventario');
//    $incidencia->setCorreo($this->getUser()->getEmail());
//    $incidencia->setDpto($nuevo_inventario->getCentroCosto()->getName());
//    $incidencia->setEstado('Solucionado');
//    $accesorio1 = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($lista_temporal[0]->getIdperiferico());
//    $incidencia->setAsesorio($accesorio1->getTipoEquipo());
//    $incidencia->setFecha(new \DateTime('now'));
//    $incidencia->setFechaA(null);
//    $incidencia->setRespuesta('Se realizo la instalacion de nuevo equipamiento');
//    $incidencia->setTecAsignado($this->getUser()->getUsername());
//    $incidencia->setTipo('Instalacion de nuevo equipamiento');
//    $incidencia->setTipoMov('Instacion de equipo');
//    $incidencia->setUser($this->getUser());


        $this->addFlash('mensaje', 'Inventario guardado correctamente');
        // dump($nombre_estacion);
        // die();
        //$this->limpiar_estacion($nombre_estacion, 'nuevo');
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($inventario_actual->getId());
        // $lista_componentes = $accesorio->getComponente();
//        dump($inventario[0]->getEquipos()[0]);
//         die();
        $listaE = $inventario[0]->getEquipos();

//        $chasis = null;
//        $fuente = null;
//        $mother = null;
//        $lector = null;
//        $teclado = null;
//        $bocina = null;
//        $micro = null;
//        $mouse = null;
//        $backup = null;
//        $impresora = null;
//        $monitor = null;
//        $ram = null;
//        $hdd = null;
//        $scanner = null;
//
//        foreach ($listaE as $e) {
//            switch ($e->getTipoEquipo()) {
//                case 'cpuchasis':
//                    $chasis = $e;
//                    $chasis->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($chasis);
//                    $entityManager->persist($chasis);
//                    foreach ($chasis->getComponente() as $c) {
//                        switch ($c->getTipoComponente()) {
//                            case 'fuente':
//                                $fuente = $c;
//                                break;
//                            case 'motherboard':
//                                $mother = $c;
//                                break;
//                            case 'mouse':
//                                $mouse = $c;
//                                break;
//                            case 'lector':
//                                $lector = $c;
//                                break;
//                            case 'teclado':
//                                $teclado = $c;
//                                break;
//                            case 'bocina':
//                                $bocina = $c;
//                                break;
//                            case 'microprocesador':
//                                $micro = $c;
//                                break;
//                            case 'ram':
//                                $ram = $c;
//                                break;
//                            case 'hdd':
//                                $hdd = $c;
//                                break;
//                        }
//
//                    }
//                    break;
//                case 'backup':
//                    $backup = $e;
//                    $backup->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($backup);
//                    $entityManager->persist($backup);
//                    break;
//                case 'impresora':
//                    $impresora = $e;
//                    $impresora->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($impresora);
//                    $entityManager->persist($impresora);
//                    break;
//                case 'monitor':
//                    $monitor = $e;
//                    $monitor->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($monitor);
//                    $entityManager->persist($monitor);
//                    break;
//            }
//
//        }
        foreach ($lista_temporal as $listaP) {
            $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($listaP->getIdperiferico());
            // $accesorio->setEstacion($nuevo_inventario);
            $inventario_actual->addEquipo($accesorio);
            $accesorio->setEstacion($inventario_actual);
            $inventario_actual->setActivo('Si');
            $inventario_actual->setEstado('Activo');
            // $accesorio->setEstacion($nuevo_inventario);
            $entityManager->persist($accesorio);
            $entityManager->persist($inventario_actual);
        }
        // dump($inventario_actual);die();
        $entityManager->flush();
//    $inventarioNuevo = $entityManager2->getRepository(inventario::class)->Todo($nuevo_inventario->getId());
//
//    $listaN = $inventarioNuevo[0]->getEquipos();
//
//    foreach ($listaN as $e) {
//      switch ($e->getTipoEquipo()) {
//        case 'cpuchasis':
//          $chasis = $e;
//          $chasis->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($chasis);
//          $entityManager->persist($chasis);
//          foreach ($chasis->getComponente() as $c) {
//            switch ($c->getTipoComponente()) {
//              case 'fuente':
//                $fuente = $c;
//                break;
//              case 'motherboard':
//                $mother = $c;
//                break;
//              case 'mouse':
//                $mouse = $c;
//                break;
//              case 'lector':
//                $lector = $c;
//                break;
//              case 'teclado':
//                $teclado = $c;
//                break;
//              case 'bocina':
//                $bocina = $c;
//                break;
//              case 'microprocesador':
//                $micro = $c;
//                break;
//              case 'ram':
//                $ram = $c;
//                break;
//              case 'hdd':
//                $hdd = $c;
//                break;
//            }
//
//          }
//          break;
//        case 'backup':
//          $backup = $e;
//          $backup->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($backup);
//          $entityManager->persist($backup);
//          break;
//        case 'impresora':
//          $impresora = $e;
//          $impresora->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($impresora);
//          $entityManager->persist($impresora);
//          break;
//        case 'monitor':
//          $monitor = $e;
//          $monitor->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($monitor);
//          $entityManager->persist($monitor);
//          break;
//        case 'scanner':
//          $scanner = $e;
//          $scanner->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($scanner);
//          $entityManager->persist($scanner);
//          break;
//      }
//
//    }
//    // $entityManager->persist($nuevo_inventario);
//        dump($listaE[0]);
//        dump($nuevo_inventario);
//        die();
        // $in = $entityManager->getRepository('AppBundle:inventarios_estacion')->findBy(['id' => $nuevo_inventario->getId()]);
        return $this->redirectToRoute("datos_estacion", ['id' => $inventario_actual->getId()]);
//        return $this->render('estacion_trabajo/datos_estacion.html.twig', ['inventario' => $nuevo_inventario, 'chasis' => $chasis, 'bocina' => $bocina, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
//            'impresora' => $impresora, 'monitor' => $monitor, 'scanner' => $scanner]);
    }

    /**
     * @Route("/reportes/estacion/salvar_inventario_nuevo_incidenci2a/{idestacion}",name="inventario_final_nuevo2")
     */
    public function crearNuevoInventariodesdeEstacionAction($idestacion)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $inventario_actual = $entityManager->getRepository('AppBundle:inventario')->find($idestacion);
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->findBy(['inventario' => $idestacion]);
        $lista_temporal = $applicationRepository->findAll();
        $nombre_estacion = $inventario_actual->getNombreRed();
        $equipo = '';
        //Creacion del nuevo inventario,lo unico que cambia son los equipos dentro
        $nuevo_inventario = new inventario();
        $nuevo_inventario->setEstado('Activo');
        $nuevo_inventario->setAntivirus($inventario_actual->getAntivirus());
        $nuevo_inventario->setAntivirus('KASP');
        $nuevo_inventario->setCentroCosto($inventario_actual->getCentroCosto());
        // $nuevo_inventario->setEstado($inventario_actual->getEstado());
        $nuevo_inventario->setFechacreacion(new \DateTime('now'));
        $nuevo_inventario->setIp($inventario_actual->getIp());
        $nuevo_inventario->setJefeInformatica($inventario_actual->getJefeInformatica());
        $nuevo_inventario->setNombreRed($inventario_actual->getNombreRed());
        $nuevo_inventario->setPassSetup($inventario_actual->getPassSetup());
        $nuevo_inventario->setResponsable($inventario_actual->getResponsable());
        $nuevo_inventario->setTecnico($this->getUser()->getUsername());
        $inventario_actual->setActivo('No');
        $inventario_actual->setEstado('Inactivo');

        // dump($nuevo_inventario);die();
//    $incidencia=new incidencia();
//    $incidencia->setInventario($nuevo_inventario);
//    $incidencia->setAsunto('Creacion de nuevo inventario');
//    $incidencia->setCorreo($this->getUser()->getEmail());
//    $incidencia->setDpto($nuevo_inventario->getCentroCosto()->getName());
//    $incidencia->setEstado('Solucionado');
//    $accesorio1 = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($lista_temporal[0]->getIdperiferico());
//    $incidencia->setAsesorio($accesorio1->getTipoEquipo());
//    $incidencia->setFecha(new \DateTime('now'));
//    $incidencia->setFechaA(null);
//    $incidencia->setRespuesta('Se realizo la instalacion de nuevo equipamiento');
//    $incidencia->setTecAsignado($this->getUser()->getUsername());
//    $incidencia->setTipo('Instalacion de nuevo equipamiento');
//    $incidencia->setTipoMov('Instacion de equipo');
//    $incidencia->setUser($this->getUser());


        $this->addFlash('mensaje', 'Inventario guardado correctamente');
        // dump($nombre_estacion);
        // die();
        //$this->limpiar_estacion($nombre_estacion, 'nuevo');
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($inventario_actual->getId());
        // $lista_componentes = $accesorio->getComponente();
//        dump($inventario[0]->getEquipos()[0]);
//         die();
        $listaE = $inventario[0]->getEquipos();

//        $chasis = null;
//        $fuente = null;
//        $mother = null;
//        $lector = null;
//        $teclado = null;
//        $bocina = null;
//        $micro = null;
//        $mouse = null;
//        $backup = null;
//        $impresora = null;
//        $monitor = null;
//        $ram = null;
//        $hdd = null;
//        $scanner = null;
//
//        foreach ($listaE as $e) {
//            switch ($e->getTipoEquipo()) {
//                case 'cpuchasis':
//                    $chasis = $e;
//                    $chasis->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($chasis);
//                    $entityManager->persist($chasis);
//                    foreach ($chasis->getComponente() as $c) {
//                        switch ($c->getTipoComponente()) {
//                            case 'fuente':
//                                $fuente = $c;
//                                break;
//                            case 'motherboard':
//                                $mother = $c;
//                                break;
//                            case 'mouse':
//                                $mouse = $c;
//                                break;
//                            case 'lector':
//                                $lector = $c;
//                                break;
//                            case 'teclado':
//                                $teclado = $c;
//                                break;
//                            case 'bocina':
//                                $bocina = $c;
//                                break;
//                            case 'microprocesador':
//                                $micro = $c;
//                                break;
//                            case 'ram':
//                                $ram = $c;
//                                break;
//                            case 'hdd':
//                                $hdd = $c;
//                                break;
//                        }
//
//                    }
//                    break;
//                case 'backup':
//                    $backup = $e;
//                    $backup->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($backup);
//                    $entityManager->persist($backup);
//                    break;
//                case 'impresora':
//                    $impresora = $e;
//                    $impresora->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($impresora);
//                    $entityManager->persist($impresora);
//                    break;
//                case 'monitor':
//                    $monitor = $e;
//                    $monitor->setEstacion($nuevo_inventario);
//                    $nuevo_inventario->addEquipo($monitor);
//                    $entityManager->persist($monitor);
//                    break;
//            }
//
//        }
        foreach ($lista_temporal as $listaP) {
            $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($listaP->getIdperiferico());
            // $accesorio->setEstacion($nuevo_inventario);
            $nuevo_inventario->addEquipo($accesorio);
            $accesorio->setEstacion($nuevo_inventario);
            $nuevo_inventario->setActivo('Si');
            $nuevo_inventario->setEstado('Activo');
            // $accesorio->setEstacion($nuevo_inventario);
            $entityManager->persist($accesorio);
            $entityManager->persist($nuevo_inventario);
        }
        $entityManager->flush();
//    $inventarioNuevo = $entityManager2->getRepository(inventario::class)->Todo($nuevo_inventario->getId());
//
//    $listaN = $inventarioNuevo[0]->getEquipos();
//
//    foreach ($listaN as $e) {
//      switch ($e->getTipoEquipo()) {
//        case 'cpuchasis':
//          $chasis = $e;
//          $chasis->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($chasis);
//          $entityManager->persist($chasis);
//          foreach ($chasis->getComponente() as $c) {
//            switch ($c->getTipoComponente()) {
//              case 'fuente':
//                $fuente = $c;
//                break;
//              case 'motherboard':
//                $mother = $c;
//                break;
//              case 'mouse':
//                $mouse = $c;
//                break;
//              case 'lector':
//                $lector = $c;
//                break;
//              case 'teclado':
//                $teclado = $c;
//                break;
//              case 'bocina':
//                $bocina = $c;
//                break;
//              case 'microprocesador':
//                $micro = $c;
//                break;
//              case 'ram':
//                $ram = $c;
//                break;
//              case 'hdd':
//                $hdd = $c;
//                break;
//            }
//
//          }
//          break;
//        case 'backup':
//          $backup = $e;
//          $backup->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($backup);
//          $entityManager->persist($backup);
//          break;
//        case 'impresora':
//          $impresora = $e;
//          $impresora->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($impresora);
//          $entityManager->persist($impresora);
//          break;
//        case 'monitor':
//          $monitor = $e;
//          $monitor->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($monitor);
//          $entityManager->persist($monitor);
//          break;
//        case 'scanner':
//          $scanner = $e;
//          $scanner->setEstacion($nuevo_inventario);
//          $nuevo_inventario->addEquipo($scanner);
//          $entityManager->persist($scanner);
//          break;
//      }
//
//    }
//    // $entityManager->persist($nuevo_inventario);
//        dump($listaE[0]);
//        dump($nuevo_inventario);
//        die();
        // $in = $entityManager->getRepository('AppBundle:inventarios_estacion')->findBy(['id' => $nuevo_inventario->getId()]);
        return $this->redirectToRoute("datos_estacion", ['id' => $nuevo_inventario->getId()]);
//        return $this->render('estacion_trabajo/datos_estacion.html.twig', ['inventario' => $nuevo_inventario, 'chasis' => $chasis, 'bocina' => $bocina, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
//            'impresora' => $impresora, 'monitor' => $monitor, 'scanner' => $scanner]);
    }


    /**
     * @Route("/reportes/estacion/salvar_inventario/{idestacion}",name="nuevo_inventario_Incidencia")
     */
    public function nuevoInventarioxIncidenciaAction($idestacion)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal');
        $applicationRepository2 = $entityManager->getRepository('AppBundle:inventario')->find($idestacion);

        $lista_temporal = $applicationRepository->findAll();
        $nombre_estacion = $applicationRepository2->getNombreRed();
        $equipo = '';

        foreach ($lista_temporal as $listaP) {


            $accesorio = $this->getDoctrine()->getRepository('AppBundle:equipo')->find($listaP->getIdperiferico());

            $applicationRepository2->addEquipo($accesorio);
            $accesorio->setEstacion($applicationRepository2);
            $entityManager->persist($accesorio);

        }
        $entityManager->persist($applicationRepository2);

        $entityManager->flush();
        //dump($applicationRepository2->getEquipo());
        // die();
        $this->addFlash('mensaje', 'Inventario guardado correctamente');
        // dump($nombre_estacion);
        // die();
        $this->limpiar_estacion($nombre_estacion);
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($idestacion);
        // $lista_componentes = $accesorio->getComponente();
        //dump($inventario[0]->getEquipo());
        // die();
        $listaE = $inventario[0]->getEquipos();
        $chasis = null;
        $fuente = null;
        $mother = null;
        $lector = null;
        $teclado = null;
        $bocina = null;
        $micro = null;
        $mouse = null;
        $backup = null;
        $impresora = null;
        $monitor = null;
        $ram = null;
        $hdd = null;

        foreach ($listaE as $e) {
            //dump($e);die();
            switch ($e->getTipoEquipo()) {
                case 'cpuchasis':
                    $chasis = $e;

                    foreach ($chasis->getComponente() as $c) {
                        switch ($c->getTipoComponente()) {
                            case 'fuente':
                                $fuente = $c;
                                break;
                            case 'motherboard':
                                $mother = $c;
                                break;
                            case 'mouse':
                                $mouse = $c;
                                break;
                            case 'lector':
                                $lector = $c;
                                break;
                            case 'teclado':
                                $teclado = $c;
                                break;
                            case 'bocina':
                                $bocina = $c;
                                break;
                            case 'microprocesador':
                                $micro = $c;
                                break;
                            case 'ram':
                                $ram = $c;
                                break;
                            case 'hdd':
                                $hdd = $c;
                                break;
                        }

                    }
                    break;
                case 'fuente':
                    $fuente = $e;
                    break;
                case 'motherboard':
                    $mother = $e;
                    break;
                case 'lector':
                    $lector = $e;
                    break;
                case 'teclado':
                    $teclado = $e;
                    break;
                case 'bocina':
                    $bocina = $e;
                    break;
                case 'micro':
                    $micro = $e;
                    break;
                case 'mouse':
                    $mouse = $e;
                    break;
                case 'backup':
                    $backup = $e;
                    break;
                case 'impresora':
                    $impresora = $e;
                    break;
                case 'monitor':
                    $monitor = $e;
                    break;
                case 'ram':
                    $ram = $e;
                    break;
                case 'hdd':
                    $hdd = $e;
                    break;
            }
        }
//dump($chasis);die();

        return $this->render('estacion_trabajo/datos_estacion.html.twig', ['inventario' => $inventario, 'chasis' => $chasis, 'bocina' => $bocina, 'fuente' => $fuente, 'motherb' => $mother, 'micro' => $micro, 'ram' => $ram, 'hdd' => $hdd, 'lector' => $lector, 'mouse' => $mouse, 'teclado' => $teclado, 'backup' => $backup,
            'impresora' => $impresora, 'monitor' => $monitor]);
    }


    /**
     * @Route("reportes/estacion/vaciar_estacion/{nombre_estacion}", name="limpiar_estacion")
     */
    public function limpiar_estacion($nombre_estacion)
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'DELETE FROM AppBundle:temporal'
        );

        $products = $query->getResult();
        //  dump($nombre_estacion);
        // die();
        return $this->redirectToRoute('refrescar_lista', ['nombre_estacion' => $nombre_estacion, 'tipo' => $tipo]);
    }

    /**
     * @Route("reportes/estacion/eliminar_de_estacion/{idperiferico}/{nombre_estacion}/{tipoAccion}/{incidencia_id}", name="eliminar_de_lista")
     */
    public function eliminar_de_estacionAction($idperiferico, $nombre_estacion, $tipoAccion, $incidencia_id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:temporal')->find($idperiferico);
//dump($tipoAccion);die();
        $periferico = $entityManager->getRepository('AppBundle:equipo');
        $applicationRepository3 = $entityManager->getRepository('AppBundle:temporal');
        $this->nombreE = $nombre_estacion;
        if ($applicationRepository != null) {

            $entityManager->remove($applicationRepository);
            $entityManager->flush();


            // return new Response("El plan con id {$id} ha sido eliminado");
            $this->addFlash('success', 'El periferico con id :' . $idperiferico . ' ha sido eliminado del inventario:' . $this->nombreE);

        }


        return $this->redirectToRoute('refrescar_lista', ['nombre_estacion' => $this->nombreE, 'tipo' => $tipoAccion, 'incidencia_id' => $incidencia_id]);

    }

    /**
     * @Route("reportes/estacion/desactivar_estacion/{id}",name="desactivar_estacion")
     */
    public function desactivarEstacionAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:inventario');

        $estacion = $applicationRepository->find($id);
        $estacion->setEstado('Inactivo');
        $entityManager->persist($estacion);
        $entityManager->flush();

        return $this->redirectToRoute('lista_estaciones', [
        ]);
    }

    /**
     * @Route("reportes/estacion/activar_estacion/{id}",name="activar_estacion1")
     */
    public function activateEstacion($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $applicationRepository = $entityManager->getRepository('AppBundle:inventario');

        $estacion = $applicationRepository->find($id);
        $estacion->setEstado('Activo');
        $entityManager->persist($estacion);
        $entityManager->flush();

        return $this->redirectToRoute('lista_estaciones');
    }


    /**
     * @Route("/estacion/buscar_componente",name="buscar_componente")
     */
    public function buscarComponenteLocalyRemotoAction($componente, $id)
    {
        $consulta = '';
        if ($componente == 'Bocinas') {
            $consulta = $this->getDoctrine()
                ->getRepository('bocinas')
                ->find($id);
        } else if (!$consulta) {
            throw $this->createNotFoundException('No existe el componente con id:' . $id);
        }
        return $this->render('nueva_estacion.html.twig', ['componente' => $consulta]);


    }

    /**
     * @Route("/reportes/estaciones/filtrar_estaciones",name="filtrar_estaciones")
     */
    public function filtraEstacionAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id_estacion = $request->get('estaciones');
        //$estado_estacion = $request->get('estado');
        // dump($request);die();
        if ($id_estacion == '') {
            $this->addFlash('alerta', 'Usted debe seleccionar el departamento, para buscar');
        }
        if ($id_estacion != '') {
            // $entityManager = $this->getDoctrine()->getManager();
            //  $applicationRepository = $entityManager->getRepository('AppBundle:inventario')->findOneBy(['id'=>$id_estacion]);
            //  $estacion = $applicationRepository;
            $em = $this->get('doctrine.orm.entity_manager');
            $dql = "SELECT a FROM AppBundle:inventario a WHERE a.id = " . $id_estacion[0] . "";
            $query = $em->createQuery($dql);
            $estacionesVacias = new ArrayCollection();
            $inventarios = $entityManager->getRepository('AppBundle:inventario')->findAll();
            $componentesBasicos = ['cpuchasis', 'monitor', 'backup'];
            foreach ($inventarios as $i) {
                $contChasis = 0;
                foreach ($i->getEquipos() as $equipo) {
                    // dump($equipo);
                    if ($equipo->getTipoEquipo() == $componentesBasicos[0]) {
                        $contChasis = $contChasis + 1;
                    }
//
                }
                if ($contChasis == 0) {
                    $estacionesVacias->add($i);
                    //   $estacionesVacias->add('chasis');
                }
//
            }


            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1), 1
            );
            $entityManager = $this->getDoctrine()->getManager();
            $areas = $entityManager->getRepository('AppBundle:area')->findAll();
            // dump($estacion);
            //  die();
            return $this->render('estacion_trabajo/lista_estaciones.html.twig', ['pagination' => $pagination, 'areas' => $areas, 'estacionesSinChasis' => $estacionesVacias]);
        } else

            $this->addFlash('alert', 'No existe inventario con los datos seleccionados');
        return $this->redirectToRoute('lista_estaciones');
    }

    /**
     * @Route("reportes/estacion/ver_ficha/{id_estacion}",name="imprimir_ficha_estacion")
     */
    public function imprimirFichaAction($id_estacion)
    {
        $entityManager2 = $this->getDoctrine()->getManager();
        $inventario = $entityManager2->getRepository(inventario::class)->Todo($id_estacion);
        $listaE = $inventario[0]->getEquipos();
        $listaC=$inventario[0]->getComponente();
        //dump($listaE);die();
        $chasis = null;
        $fuente = null;
        $mother = null;
        $lector = new ArrayCollection();
        $teclado = null;
        $bocina = null;
        $micro = null;
        $mouse = null;
        $backup = null;
        $estabilizador=null;
        $impresoras = new ArrayCollection();
        $monitores = new ArrayCollection();
        $ram = new ArrayCollection();
        $hdd = new ArrayCollection();
        $tarjetaVideo = null;

        foreach ($listaE as $e) {
            //dump($e);die();
            switch ($e->getTipoEquipo()) {
                case 'cpuchasis':
                    $chasis = $e;
                    break;
                case 'backup':
                    $backup = $e;
                    break;
                case 'impresora':
                    $impresoras->add($e);
                    break;
                case 'monitor':
                    $monitores->add($e);
                    break;
                case 'tarjeta_video':
                    $tarjetaVideo = $e;
                    break;
                case 'estabilizador':
                    $estabilizador = $e;
                    break;
            }

        }
        foreach ($listaC as $c) {
            switch ($c->getTipoComponente()) {
                case 'fuente':
                    $fuente = $c;
                    break;
                case 'motherboard':
                    $mother = $c;
                    break;
                case 'mouse':
                    $mouse = $c;
                    break;
                case 'lector':
                    $lector->add($c);
                    break;
                case 'teclado':
                    $teclado = $c;
                    break;
                case 'bocina':
                    $bocina = $c;
                    break;
                case 'microprocesador':
                    $micro = $c;
                    break;
                case 'ram':
                    $ram->add($c);
                    break;
                case 'hdd':
                    $hdd->add($c);
                    break;
            }

        }


        $snappy = $this->get('knp_snappy.pdf');
      //  dump($listaE);die();
        $html = $this->renderView('reportes/ficha_inventario.html.twig', ['inventario' => $inventario[0], 'title' => 'ficha_inventario', 'chasis' => $chasis, 'fuente' => $fuente, 'board' => $mother
            , 'micro' => $micro, 'ram' => $ram,'estabilizador'=> $estabilizador, 'hdd' => $hdd, 'lector' => $lector, 'tarjetaVideo' => $tarjetaVideo, 'mouse' => $mouse, 'teclado' => $teclado, 'bocinas' => $bocina, 'backup' => $backup, 'monitores' => $monitores, 'impresoras' => $impresoras]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html), 200, array(

                'Content-Type' => 'application/pdf',
                'encoding' => 'utf-8',

                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }


    /**
     * @Route("reportes/estacion/ver_ficha",name="imprimir_equipos_en_taller")
     */
    public function imprimirEntallerAction()
    {
        $lista = $this->getDoctrine()
            ->getRepository('AppBundle:taller')
            ->findAll();
        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/ficha_taller.html.twig', ['lista' => $lista]);
//        dump($lista);
//        die();
        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html, array(
                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
            )), 200, array(

                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'page-size' => 'A4',

                'viewport-size' => '1280x1024',
                'margin-left' => '10mm',

                'margin-right' => '10mm',

                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }

    /**
     * @Route("reportes/estacion/equipos_en_almacen",name="imprimir_equipos_en_almacen")
     */
    public function imprimirEnAlmacenAction()
    {
        $estacion = $this->getDoctrine()
            ->getRepository('AppBundle:inventario')->find(3861);
        $lista = $this->getDoctrine()
            ->getRepository('AppBundle:equipo')
            ->findBy(['estacion' => $estacion]);
        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/ficha_almacen.html.twig', ['lista' => $lista]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html, array(
                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
            )), 200, array(

                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'page-size' => 'A4',

                'viewport-size' => '1280x1024',
                'margin-left' => '10mm',

                'margin-right' => '10mm',

                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }

    /**
     * @Route("reportes/estacion/lista_equipos_pendientes_taller",name="imprimir_equipos_pendientes_taller")
     */
    public function imprimirPenditentesTallerAction(Request $request)
    {
        //dump($request);die();
        $lista = $request->query->get('lista');
//        $estacion = $this->getDoctrine()
//            ->getRepository('AppBundle:inventario')->find(3861);
//        $lista = $this->getDoctrine()
//            ->getRepository('AppBundle:equipo')
//            ->findBy(['estacion' => $estacion]);
        $snappy = $this->get('knp_snappy.pdf');
        $snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/lista_pendientes_taller.html.twig', ['lista' => $lista]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html, array(
                'user-style-sheet' => 'build/css/estilos-tabla-taller.css'
            )), 200, array(

                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'page-size' => 'A4',

                'viewport-size' => '1280x1024',
                'margin-left' => '10mm',

                'margin-right' => '10mm',

                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }


    /**
     * @Route("/reportes/estaciones/remove_estacion/{id}", name="remove_estacion")
     */
    public function removeEstacionAction($id)
    {
        $post = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'No se ha encontrado ninguna estacion con este ' . $id
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();

        // return new Response("El plan con id {$id} ha sido eliminado");
        $this->addFlash('success', 'La estacion con id :' . $id . ' ha sido eliminada');
        return $this->redirectToRoute('lista_estaciones');
    }

    /**
     * @Route("/reportes/nuevo_chasis",name="nuevo_chasis2")
     *
     */
    public function nuevoChasis(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $cpu = new equipo();
        $cpu->setTipoEquipo('cpuchasis');

        $fuente = new componente();
        $fuente->setTipoComponente('fuente');

        $board = new componente();
        $board->setTipoComponente('motherboard');

        $micro = new componente();
        $micro->setTipoComponente('microprocesador');

        $ram = new componente();
        $ram->setTipoComponente('ram');

        $hdd = new componente();
        $hdd->setTipoComponente('hdd');

        $lector = new componente();
        $lector->setTipoComponente('lector');

        $mouse = new componente();
        $mouse->setTipoComponente('mouse');

        $bocinas = new componente();
        $bocinas->setTipoComponente('bocinas');

        $teclado = new componente();
        $teclado->setTipoComponente('teclado');

        $tv = new componente();
        $tv->setTipoComponente('tarjetaVideo');


        $cpu->addComponente($fuente);
        $cpu->addComponente($board);
        $cpu->addComponente($micro);
        $cpu->addComponente($ram);
        $cpu->addComponente($hdd);
        $cpu->addComponente($lector);
        $cpu->addComponente($mouse);
        $cpu->addComponente($bocinas);
        $cpu->addComponente($teclado);
        $cpu->addComponente($tv);


        $form = $this->createForm(chasisFormType::class, $cpu);
        $form->handleRequest($request);
        //  dump($form->getData()->getcomponente());die();
        // dump($cpu);die();
        //dump($form->get('impresora'));die();
        if ($form->isSubmitted() && $form->isValid()) {
            //dump($cpu);die();

            $cpu->setEstado('Sin estacion de trabajo');


            $em->persist($cpu);
            $em->flush();


            /*return $this->render('estacion_trabajo/ver_datos_cpuchasis.html.twig', ['datos' => $cpu, 'board' => $cpu->getboard(), 'bocina1' => $cpu->getBocina1()
        , 'fuente' => $cpu->getFuente(), 'hdd' => $cpu->getDiscoD(), 'lector' => $cpu->getLectorCD(), 'micro' => $cpu->getMicro(), 'mouse' => $cpu->getRaton(), 'ram' => $cpu->getMemoriaRam(), 'scanner' => $cpu->getScan(), 'tarjeta_video' => $cpu->getTarjetaVideo(), 'teclado' => $cpu->getTeclado1(),
      ]);*/
            return $this->redirectToRoute('ver_datos_periferico', ['id' => $cpu->getId(), 'tipo' => 'cpuchasis']);
        }

//dump($form->createView());die();
        $form->add('Guardar', SubmitType::class, array('label' => 'Guardar'));
        return $this->render('estacion_trabajo/nuevo_cpuchasis.html.twig', ['form' => $form->createView(),
        ]);


    }


    /**
     * @Route("/reportes/nuevo_chasis2/{nombre_estacion}/{idestacion}",name="adicionarChasisE")
     *
     */
    public function nuevo2Chasis(Request $request, $nombre_estacion, $idestacion)
    {
        //$idE=$request->request->get('idestacion');
        $em = $this->getDoctrine()->getManager();
        $cpu = new cpuchasis();

        $m = new motherboard();
        $m->setEstado('Nuevo');
        //$cpu->getboard()->add($m);
        $cpu->addBoard($m);
        // dump($cpu);die();
        $b = new bocinas();
        $b->setEstado('Nuevo');
        //$cpu->getBocina1()->add($b);
        $cpu->addBocina($b);
        $f = new fuente();
        // $cpu->getFuente()->add($f);
        $cpu->addFuente($f);
        $h = new hdd();
        //$cpu->getDiscoD()->add($h);
        $cpu->addHdd($h);
        $l = new lector();
        $cpu->addLector($l);
        // $cpu->getLectorCD()->add($l);
        $mi = new microprocesador();
        $cpu->addMicro($mi);
        //$cpu->getMicro()->add($mi);
        $mo = new mouse();
        $cpu->addMouse($mo);
        // $cpu->getRaton()->add($mo);
        $r = new ram();
        $cpu->addRam($r);
        //$cpu->getMemoriaRam()->add($r);
        $tv = new tarjeta_video();
        //$cpu->getTarjetaVideo()->add($tv);
        $cpu->addTv($tv);
        $teclado = new teclado();
        $cpu->addTeclado($teclado);
        //$cpu->getTeclado1()->add($teclado);
        /*$impresora = new impresora();
    $cpu->getImpresora()->add($impresora);
    //dump($cpu);die();
    $estabilizador = new estabilizador();
    $cpu->getEstabilizador()->add($estabilizador);
    $backup = new backup();
    $cpu->getBackup()->add($backup);
    $scanner = new scanner();
    $cpu->getScanner()->add($scanner);
    $monitor = new monitor();
    $cpu->getMonitor()->add($monitor);*/
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->findBy(['id' => $idestacion]);

        // dump($idestacion);die();

        $form = $this->createForm(chasisFormType::class, $cpu);
        $form->handleRequest($request);
        // dump($cpu);die();
        //dump($form->get('impresora'));die();
        if ($form->isSubmitted() && $form->isValid()) {
            //dump($cpu);die();
            foreach ($cpu->getLectorCD() as $l) {
                // $l->setCpu($cpu->getId());
                $l->setNumInventario($cpu->getNumInventario());
                $l->setEstado('Activo');

                $em->persist($l);
                //dump($b);die();

            }
            foreach ($cpu->getRaton() as $mo) {
                // $mo->setCpu($cpu->getId());
                $mo->setNumInventario($cpu->getNumInventario());
                $mo->setEstado('Activo');

                $em->persist($mo);
                //dump($b);die();

            }
            foreach ($cpu->getTeclado1() as $t) {
                //  $t->setCpu($cpu->getId());
                $t->setNumInventario($cpu->getNumInventario());
                $t->setEstado('Activo');
                $em->persist($t);
                //dump($b);die();

            }
            foreach ($cpu->getBocina1() as $bo) {
                //$bo->setCpu($cpu->getId());
                $bo->setNumInventario($cpu->getNumInventario());
                $bo->setEstado('Activo');
                $em->persist($bo);
                //dump($b);die();

            }

            foreach ($cpu->getTarjetaVideo() as $tv) {
                // $tv->setCpu($cpu->getId());
                $tv->setNumInventario($cpu->getNumInventario());
                $tv->setEstado('Activo');
                $em->persist($tv);
                //dump($b);die();

            }


            foreach ($cpu->getBoard() as $b) {
                if ($b != null) {
                    //$b->setCpu($cpu->getId());
                    $b->setNumInventario($cpu->getNumInventario());
                    $b->setEstado('Activo');
                    $em->persist($b);

                    //dump($b);die();
                }
            }

            foreach ($cpu->getFuente() as $f) {
                //$f->setCpu($cpu->getId());
                $f->setNumInventario($cpu->getNumInventario());
                $f->setEstado('Activo');
                $em->persist($f);
                //dump($f);die();

            }

            foreach ($cpu->getMicro() as $m) {
                //$m->setCpu($cpu->getId());
                $m->setNumInventario($cpu->getNumInventario());
                $m->setEstado('Activo');
                $em->persist($m);
                //dump($b);die();

            }
            foreach ($cpu->getMemoriaRam() as $r) {
                // $r->setCpu($cpu->getId());
                $r->setNumInventario($cpu->getNumInventario());
                $r->setEstado('Activo');
                $em->persist($r);
                //dump($b);die();

            }
            foreach ($cpu->getDiscoD() as $h) {
                // $h->setCpu($cpu->getId());
                $h->setNumInventario($cpu->getNumInventario());
                $h->setEstado('Activo');
                $em->persist($h);
                //dump($b);die();

            }
            $cpu->setEstado('Activo');

//dump($estacion[0]);die();

            $cpu->setInv($estacion[0]);
            $em->persist($cpu);
            $estacion[0]->setChasis($cpu);
            $em->persist($estacion[0]);
            $em->flush();

            $tipo = 'cpuchasis';

            //dump($cpu);die();

            /*return $this->render('estacion_trabajo/ver_datos_cpuchasis.html.twig', ['datos' => $cpu, 'board' => $cpu->getboard(), 'bocina1' => $cpu->getBocina1()
        , 'fuente' => $cpu->getFuente(), 'hdd' => $cpu->getDiscoD(), 'lector' => $cpu->getLectorCD(), 'micro' => $cpu->getMicro(), 'mouse' => $cpu->getRaton(), 'ram' => $cpu->getMemoriaRam(), 'scanner' => $cpu->getScan(), 'tarjeta_video' => $cpu->getTarjetaVideo(), 'teclado' => $cpu->getTeclado1(),
      ]);*/
            return $this->redirectToRoute('datos_estacion', ['id' => $estacion[0]->getId()]);
        }

//dump($form->createView());die();
        $form->add('Guardar', SubmitType::class, array('label' => 'Guardar'));
        return $this->render('estacion_trabajo/nuevo_cpuchasis.html.twig', ['form' => $form->createView()]);


    }

    /**
     * @Route("/reportes/asignar_nuevo_chasis/{inventario}",name="nuevo_chasis_a_inventario")
     *
     */
    public function nuevoChasisInventario(Request $request, $inventario)
    {
        $estacion = $this->getDoctrine()->getRepository('AppBundle:inventario')->find($inventario);


        //dump($estacion);die();
        $em = $this->getDoctrine()->getManager();
        $cpu = new cpuchasis();
        $m = new motherboard();
        $m->setEstado('Activo');
        //$m->setInventario($estacion);
        $cpu->addBoard($m);
        $b = new bocinas();
        $b->setEstado('Activo');
        //$b->setCpu($estacion);
        $cpu->addBocina($b);
        $f = new fuente();
        $f->setEstado('Activo');
        // $f->setInventario($estacion);
        $cpu->addFuente($f);
        $h = new hdd();
        $h->setEstado('Activo');
        //$h->setInventario($estacion);
        $cpu->addHdd($h);
        $l = new lector();
        $l->setEstado('Activo');
        //$l->setInventario($estacion);
        $cpu->addLector($l);
        $mi = new microprocesador();
        $mi->setEstado('Activo');
        // $mi->setInventario($estacion);
        $cpu->addMicro($mi);
        $mo = new mouse();
        $mo->setEstado('Activo');
        //$mo->setInventario($estacion);
        $cpu->addMouse($mo);
        $r = new ram();
        $r->setEstado('Activo');
        //$r->setInventario($estacion);
        $cpu->addRam($r);

        $tv = new tarjeta_video();
        $tv->setEstado('Activo');
        // $tv->setInventario($estacion);
        $cpu->addTv($tv);
        $teclado = new teclado();
        $teclado->setEstado('Activo');
        //$teclado->setInventario($estacion);
        $cpu->addTeclado($teclado);


        $form = $this->createForm(chasisFormType::class, $cpu);
        $form->handleRequest($request);

        // dump($form);die();
        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($cpu->getLectorCD() as $l) {
                $l->setCpu($cpu);
                $em->persist($l);
                //dump($b);die();

            }
            foreach ($cpu->getRaton() as $mo) {
                $mo->setCpu($cpu);
                $em->persist($mo);
                //dump($b);die();

            }
            foreach ($cpu->getTeclado1() as $t) {
                $t->setCpu($cpu);
                $em->persist($t);
                //dump($b);die();

            }
            foreach ($cpu->getBocina1() as $bo) {
                $bo->setCpu($cpu);
                $em->persist($bo);
                //dump($b);die();

            }

            foreach ($cpu->getTarjetaVideo() as $tv) {
                $tv->setCpu($cpu);
                $em->persist($tv);
                //dump($b);die();

            }

            foreach ($cpu->getBoard() as $b) {
                if ($b != null) {
                    $b->setCpu($cpu);
                    $b->setnumInventario('--');
                    $em->persist($b);
                    //dump($b);die();
                }
            }

            foreach ($cpu->getFuente() as $f) {
                $f->setCpu($cpu);
                $em->persist($f);
                //dump($b);die();

            }

            foreach ($cpu->getMicro() as $m) {
                $m->setCpu($cpu);
                $em->persist($m);
                //dump($b);die();

            }
            foreach ($cpu->getMemoriaRam() as $r) {
                $r->setCpu($cpu);
                $em->persist($r);
                //dump($b);die();

            }
            foreach ($cpu->getDiscoD() as $h) {
                $h->setCpu($cpu);
                $em->persist($h);
                //dump($b);die();

            }
            // dump($cpu);die();
            $cpu->setEstado('Activo');
            $cpu->setInv($estacion);
            $em->persist($cpu);
            $em->flush();

            $tipo = 'cpuchasis';

            //   dump($cpu);die();

            return $this->redirectToRoute('datos_estacion', ['id' => $estacion->getId()]);
        }


        $form->add('Guardar', SubmitType::class, array('label' => 'Guardar'));
        return $this->render('estacion_trabajo/nuevo_cpuchasis.html.twig', ['form' => $form->createView()]);


    }

    /**
     * @Route("/reportes/taller/{id}/{tipoPeriferico}", name="cambiar_fecha_salida_a_taller")
     */
    public function fechaSalidaAction(Request $request, $id, $tipoPeriferico)
    {
        /*$em = $this->getDoctrine()->getManager();
    $qb = $em->createQueryBuilder();
    $query = $qb->select('AppBundle:taller', 't')
      ->where('t.tipo_periferico = :tipo')
      ->setParameter('tipo', $tipoPeriferico)
      ->andWhere('t.id_periferico = :id')
      ->setParameter('id', $id)
      ->getQuery();*/

        $repository = $this->getDoctrine()->getRepository(taller::class);
        $equipo = $repository->findOneBy([
            'tipo_periferico' => $tipoPeriferico,
            'id' => $id
        ]);


        $equipo->setFechaSalida($request->request->get('fechaSalida'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($equipo);
        $em->flush();

        // return new Response("El plan con id {$id} ha sido eliminado");
        $this->addFlash('success', 'Se ha asignado correctamente la fecha de salida de taller ');
        return $this->redirectToRoute('lista_equipos_taller');
    }

    /**
     * @Route("reportes/inventarios_estacion/{maxItemPerPage}/{idestacion}", name="lista_inventarios_estacion")
     */
    public function listaInventtariosEstacionAction(Request $request, $idestacion, $maxItemPerPage = 10)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $inventario_inicial = $entityManager->getRepository('AppBundle:inventario')->find($idestacion);
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:inventario');
        $inv = $repository->createQueryBuilder('tabla')
            ->where('tabla.nombreRed = :ide')
            ->setParameter('ide', $inventario_inicial->getNombreRed())
            ->orderBy('tabla.id', 'DESC')
            ->getQuery()->getResult();
        //dump($inv);die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $inv,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        return $this->render(
            'estacion_trabajo/inventarios_estacion.html.twig', array('pagination' => $pagination, 'inventarios' => $inv, 'estacion' => $inventario_inicial

        ));
    }

    /**
     * @Route("reportes/equipos_en_almacen/{maxItemPerPage}",name="lista_equipos_en_almacen")
     */
    public function listaEnAlamcenAction(Request $request, $maxItemPerPage = 10)
    {

//    $lista = $this->getDoctrine()
//      ->getRepository('AppBundle:taller')
//      ->findAll();
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:equipo');
        $almacen = $this->getDoctrine()
            ->getRepository('AppBundle:inventario')->find(3861);
        $lista = $repository->createQueryBuilder('tabla')
            ->where('tabla.estacion=:estacion')
            ->setParameter('estacion', $almacen)
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery();
//    dump($lista->execute());
//    die();
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $lista,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        //  dump($pagination);die();
        $fecha = $tipoForm = $this->createForm('AppBundle\Form\fechaSalidaTaller');
        //  $estacionForm = $this->createForm('AppBundle\Form\EstacionForm');
        $fecha->handleRequest($request);
        // dump($fecha);die();
        if ($fecha->isSubmitted() && $fecha->isValid()) {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:inventario');
            $estacion = $repository->createQueryBuilder('tabla')
                ->where('tabla.nombreRed=:nombre')
                ->setParameter('nombre', 'Almacen de Computacion')
                ->andWhere('tabla.id =:id')
                ->setParameter('id', $fecha->getData()->getidPeriferico())
                ->orderBy('tabla.fecha', 'desc')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            $entityManager = $this->getDoctrine()->getManager();
            // dump($fecha->getData()->getidPeriferico());die();
            $estacion->setFechaSalida($fecha->getData()->getfechaSalida());
            $entityManager->persist($estacion);
            $entityManager->flush();


            return $this->redirectToRoute('lista_equipos_en_almacen');
            /* return $this->render('estacion_trabajo/adicionar_componentes.html.twig', array(
         //'estacionForm' => $estacionForm->createView(),
         'id'=>$this->idactual

       ));*/

        } else
            return $this->render('reportes/equipos_en_almacen.html.twig', array('pagination' => $pagination, 'lista' => $lista, 'inventario' => $almacen, 'form' => $fecha->createView()));
    }

    /**
     * @Route("reportes/deudas_taller/{maxItemPerPage}",name="lista_deudas_taller")
     */
    public function listaDeudastallerAction(Request $request, $maxItemPerPage = 10)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $lista = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipo=:tipo')
            ->setParameter('tipo', 'Pc defectuosa')
            ->andWhere('tabla.respuesta=:resp')
            ->setParameter('resp', 'Pc defectuosa')
            ->andWhere('tabla.estado=:estadoIncidencia')
            ->setParameter('estadoIncidencia', 'Activa')
            ->orderBy('tabla.id', 'desc')
            ->getQuery();
        $piezasConDeuda = [];
        $i = 0;
        $arreglo = $lista->getResult();
        // dump($lista);die();
        $deudas = [];
        $deudas2 = new ArrayCollection();
        $limite = sizeof($arreglo);
        $pos = 0;
        foreach ($arreglo as $l) {
//            dump($l->getNumInventario());
            $chasisActual = $entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario' => $l->getNumInventario()]);
            $repository2 = $this->getDoctrine()
                ->getRepository('AppBundle:deuda');
            $piezaDeuda = $repository2->createQueryBuilder('tabla')
                ->where('tabla.cpu = :chasis')
                ->setParameter('chasis', $chasisActual)
//                ->andWhere('tabla.deuda=:tiene')
//                ->setParameter('tiene', 'Si')
                ->getQuery()->getResult();
            //dump($chasisActual);
            //   dump($piezaDeuda);
            if ($piezaDeuda != []) {
                // dump($piezaDeuda);
//                if(is_iterable($piezaDeuda)){
                //$arreglo[sizeof($arreglo)]=$piezaDeuda;
                //for ($pos=0;$pos<sizeof($piezaDeuda);$pos++){

                $deudas[$pos] = $piezaDeuda;
//                        dump($piezasConDeuda);
                // }
                //  $arreglo[$limite][$pos]=$piezaDeuda;
            } else {
                $pos = strrpos($l->getResumen(), ": ");
                $resumen = substr($l->getResumen(), $pos);
                $piezasDe = explode(' ', $resumen);
                $total = sizeof($piezasDe);
//                dump($total);
//                die();
                $posicion = 0;
                for ($i = 1; $i < $total; $i++) {
                    $repository2 = $this->getDoctrine()
                        ->getRepository('AppBundle:deuda');
//                    $piezaDeuda2 = $repository2->createQueryBuilder('tabla')
//                        ->where('tabla.cpu = :chasis')
//                        ->setParameter('chasis', $chasisActual)
//                    ->andWhere('tabla.componente=:tipo')
//                    ->setParameter('tipo',$piezasDe[$i] )
//                        ->getQuery()->getResult();
                    $em = $this->get('doctrine.orm.entity_manager');
                    $qb = $em->createQueryBuilder();
                    $qb->select('t');
                    $qb->from('AppBundle:deuda', 't');
                    $qb->where('t.cpu=:chasis');
                    $qb->setParameter('chasis', $chasisActual);
                    $qb->andWhere('t.tipoComponente=:tipo');
                    $qb->setParameter('tipo', $piezasDe[$i]);


                    //       dump($piezasDe[$i]);
                    $query = $qb->getQuery();
                    $results = $query->getResult();
                    foreach ($results as $r) {
                        //    dump($r);
                        if ($r != null) {
                            $deudas[$posicion] = $r;
                        }
                    }
                    //  dump($results);
                    // dump($piezaDeuda2[0]->);
                    //   $deudas[$posicion] = $piezaDeuda2[0]->getComponente();
//                    if($piezaDeuda!=null){
//                        dump('aqui');
//                        $deudas[$posicion] = $piezaDeuda2->getComponente();
//                        $deudas2->add($piezaDeuda2);
//                    }
                    $posicion = $posicion + 1;
                }
            }
            $pos = $pos + 1;
        }
        //   dump($posicion);
        // dump($lista->getResult());
//        dump($deudas);
//        dump($deudas);
//        die();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $lista->getResult(),
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
//        dump($lista->execute());
//        die();

        return $this->render('reportes/deudas_taller.html.twig', array('pagination' => $pagination, 'lista' => $lista, 'deudas' => $deudas));
    }

    /**
     * @Route("reportes/deudas_taller/saldar_deudas/{idIncidencia}/{idChasis}/{numI}/", name="saldo_deudas_taller")
     */
    public function saldarDeudasAction(Request $request, $idIncidencia, $idChasis, $numI)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $chasis = $entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario' => $numI]);
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($idIncidencia);
        $piezasDeudas = null;
        if ($chasis[0]->getEstacion() != null) {
            $repository2 = $this->getDoctrine()
                ->getRepository('AppBundle:deuda');
            $piezasDeudas = $repository2->createQueryBuilder('tabla')
                ->where('tabla.cpu = :chasis')
                ->setParameter('chasis', $chasis[0])
//                ->andWhere('tabla.deuda=:tiene')
//                ->setParameter('tiene', 'Si')
                ->getQuery()->getResult();
        } else {
            $repository1 = $this->getDoctrine()
                ->getRepository('AppBundle:deuda');
            $piezasDeudas = $repository1->createQueryBuilder('tabla')
                ->where('tabla.cpu = :chasis')
                ->setParameter('chasis', $chasis[0])
                ->getQuery()->execute();
//            dump($chasis[0]);
//            dump($piezasDeudas);die();
        }

//        dump($chasis);
//        dump($piezasDeudas);die();
        return $this->render('estacion_trabajo/saldar_deuda_taller.html.twig', ['chasis' => $chasis[0], 'incidencia' => $incidencia, 'componentes' => $chasis[0]->getComponente(), 'deudas' => $piezasDeudas]);
    }

    /**
     * @Route("reportes/deudas_taller/deudaIncidencia/{idChasis}/{idIncidencia}", name="saldar_deuda")
     *
     */
    public function deudaIncidenciaAction(Request $request, $idChasis, $idIncidencia)
    {
        //      dump($request);die();
        $entityManager = $this->getDoctrine()->getManager();
        $chasis = $entityManager->getRepository('AppBundle:equipo')->find($idChasis);
        $piezasRepuestas = $request->request->get('partesP');
        $incidencia = $entityManager->getRepository('AppBundle:incidencia')->find($idIncidencia);
        $user = $this->getUser();
        $incidencia_entregaPiezas = new incidencia();
        $incidencia_entregaPiezas->setTipo('Entrega de partes y piezas');
        $incidencia_entregaPiezas->setUser($user->getUsername());
        $incidencia_entregaPiezas->setDpto($incidencia->getDpto());
        $incidencia_entregaPiezas->setAsunto('Entrega de partes y piezas');
        $piezasN = '';
        $piezasConDeuda = null;
        $piezasConDeudaSinChasis = new ArrayCollection();
        $piezasEnDeuda = '';
        // dump($elemento)

        if ($chasis->getEstacion() != null) {

            $repository2 = $this->getDoctrine()
                ->getRepository('AppBundle:componente');
            $piezasConDeuda = $repository2->createQueryBuilder('tabla')
                ->where('tabla.cpu = :chasis')
                ->setParameter('chasis', $chasis)
                ->andWhere('tabla.deuda=:tiene')
                ->setParameter('tiene', 'Si')
                ->getQuery()
                ->getResult();
            for ($e = 0; $e < sizeof($piezasConDeuda); $e++) {
                for ($i = 0; $i < sizeof($piezasRepuestas); $i++) {
                    $elemento = $piezasRepuestas[$i];
//                    $piezasN = $piezasN . ' ' . $elemento;
                    $piezaDeuda = $repository2->createQueryBuilder('tabla')
                        ->where('tabla.cpu = :chasis')
                        ->setParameter('chasis', $chasis)
                        ->andWhere('tabla.deuda=:tiene')
                        ->setParameter('tiene', 'Si')
                        ->andWhere('tabla.tipoComponente =:tipo')
                        ->setParameter('tipo', $elemento)
                        ->getQuery()->getResult();
                    //dump($piezaDeuda);die();
                    if ($piezaDeuda != []) {
                        $piezaDeuda[0]->setDeuda('No');
                        $entityManager->persist($piezaDeuda[0]);
                        $em = $this->getDoctrine()->getManager();
                        $qb = $em->createQueryBuilder();
                        $query = $qb->delete('AppBundle:deuda', 't')
                            ->where('t.cpu = :chasis')
                            ->setParameter('chasis', $chasis)
                            ->andWhere('t.tipoComponente =:tipo')
                            ->setParameter('tipo', $elemento)
                            ->getQuery();
                        $query->execute();
                    }
                }
            }
            $totalDeudas = sizeof($piezasConDeuda);
//        dump($totalDeudas);
            $totalRepuestas = sizeof($piezasRepuestas);

            if ($totalRepuestas == $totalDeudas || $totalDeudas == 0) {
                $incidencia->setEstado('Solucionado');
                $incidencia->setResumen(
                    'El inventario ' . $incidencia->getInventario() . ' tenia ' . $totalRepuestas . ' piezas con deudas las cuales han sido saldadas desde el taller'
                );
            } else {
                for ($i = 0; $i < $totalDeudas; $i++) {
                    $elemento = $piezasConDeuda[$i]->getTipoComponente();
                    $piezasEnDeuda = $piezasEnDeuda . ' ' . $elemento;
                };
                $incidencia->setResumen(
                    'Total de piezas con defectos:' . sizeof($piezasConDeuda) . '   ' .
                    'Piezas:' . $piezasEnDeuda
                );
            }
            for ($i = 0; $i < sizeof($piezasRepuestas); $i++) {
                $elemento = $piezasRepuestas[$i];
                $piezasN = $piezasN . ' ' . $elemento;
            }

//            dump($piezasN);
//            dump($piezasRepuestas);
//            die();


        } else {
            //  dump($request);die();
            $repository2 = $this->getDoctrine()
                ->getRepository('AppBundle:deuda');
            $piezasConDeudaSinChasis = $repository2->createQueryBuilder('tabla')
                ->where('tabla.cpu = :chasis')
                ->setParameter('chasis', $chasis)
                ->getQuery()
                ->getResult();
            for ($e = 0; $e < sizeof($piezasConDeudaSinChasis); $e++) {
                for ($i = 0; $i < sizeof($piezasRepuestas); $i++) {
                    $elemento = $piezasRepuestas[$i];
                    $piezaDeuda = $repository2->createQueryBuilder('tabla')
                        ->where('tabla.cpu = :chasis')
                        ->setParameter('chasis', $chasis)
                        ->andWhere('tabla.tipoComponente =:tipo')
                        ->setParameter('tipo', $elemento)
                        ->getQuery()->getResult();

                    if ($piezaDeuda != []) {
                        $em = $this->getDoctrine()->getManager();
                        $qb = $em->createQueryBuilder();
                        $query = $qb->delete('AppBundle:deuda', 't')
                            ->where('t.cpu = :chasis')
                            ->setParameter('chasis', $chasis)
                            ->andWhere('t.tipoComponente =:tipo')
                            ->setParameter('tipo', $elemento)
                            ->getQuery();
                        $query->execute();
                    }
                }
            }

            $totalDeudas = sizeof($piezasConDeudaSinChasis);
//        dump($totalDeudas);
            $totalRepuestas = sizeof($piezasRepuestas);

            if ($totalRepuestas == $totalDeudas || $totalDeudas == 0) {
                $incidencia->setEstado('Solucionado');
                $incidencia->setResumen(
                    'El inventario ' . $incidencia->getInventario() . ' tenia ' . $totalRepuestas . ' piezas con deudas las cuales han sido saldadas desde el taller'
                );
            } else {
                for ($i = 0; $i < $totalDeudas; $i++) {
                    $elemento = $piezasConDeudaSinChasis[$i]->getTipoComponente();
                    $piezasEnDeuda = $piezasEnDeuda . ' ' . $elemento;
                };
                $incidencia->setResumen(
                    'Total de piezas con defectos:' . sizeof($piezasConDeudaSinChasis) . '   ' .
                    'Piezas:' . $piezasEnDeuda
                );
            }
            for ($i = 0; $i < sizeof($piezasRepuestas); $i++) {
                $elemento = $piezasRepuestas[$i];
                $piezasN = $piezasN . ' ' . $elemento;
            }
            //   dump($totalDeudas);
            //  dump($incidencia);
            //   die();
        }


        $entityManager->flush();
//        dump($piezasConDeudaSinChasis);
//        die();
//        $piezasOriginales = substr($incidencia->getResumen(), 41);
//        $piezasOriginalesArray = explode(" ", $piezasOriginales);
//        $piezasOriginalesArray1 = explode(" ", $piezasOriginales);
//       dump($piezasOriginales);
//        dump($piezasOriginalesArray);
//        $total = sizeof($piezasRepuestas);
//        $piezasN = '';
//        for ($i = 0; $i < $total; $i++) {
//            $elemento = $piezasRepuestas[$i];
//            $piezasN = $piezasN . ' ' . $elemento;
//        };
        $incidencia_entregaPiezas->setResumen(
            'Total de piezas entregadas:' . sizeof($piezasRepuestas) . '   ' .
            'Piezas:' . $piezasN
        );
        $incidencia_entregaPiezas->setTipoMov('Pc defectuosa');
        $incidencia_entregaPiezas->setTecAsignado($user);
        $zonaHoraria = new \DateTimeZone('America/Cuiaba');
        $fecha_actual = new \DateTime("now", $zonaHoraria);
        $incidencia_entregaPiezas->setFechaA($fecha_actual);
        $incidencia_entregaPiezas->setFecha($fecha_actual);
        $incidencia_entregaPiezas->setAsesorio('cpuchasis');
        $incidencia_entregaPiezas->setNumInventario($chasis->getNumInventario());
        $incidencia_entregaPiezas->setIdE($chasis->getId());
        $incidencia_entregaPiezas->setEstado('Solucionado');
        $incidencia_entregaPiezas->setRespuesta('Solucionado');
        $incidencia_entregaPiezas->setInventario($incidencia->getInventario());


//        if ($chasis->getEstacion() != null) {
//            $totalDeudas = sizeof($piezasConDeuda);
////        dump($totalDeudas);
//            $totalRepuestas = sizeof($piezasRepuestas);
//
//            if ($totalDeudas >= $totalRepuestas) {
//                for ($i = 0; $i < $totalDeudas; $i++) {
//                    $elemento = $piezasConDeuda[$i]->getTipoComponente();
////                dump($elemento);
////                die();
//                    $piezasEnDeuda = $piezasEnDeuda . ' ' . $elemento;
//                };
//                $incidencia->setResumen(
//                    'Total de piezas con defectos:' . sizeof($piezasConDeuda) . '   ' .
//                    'Piezas:' . $piezasEnDeuda
//                );
//                //  dump("entre aqui aaa");
//            } else {
//                //dump('h');
//                if ($totalDeudas == 0) {
//                    $incidencia->setEstado('Solucionado');
//                    $incidencia->setResumen(
//                        'El inventario ' . $incidencia->getInventario() . ' tenia ' . $totalRepuestas . ' piezas con deudas las cuales han sido saldadas desde el taller'
//                    );
//                }
//
//            }
//        } else {
//            $totalDeudas = sizeof($piezasConDeudaSinChasis);
//            $totalRepuestas = sizeof($piezasRepuestas);
//
//            //falta  borrar la deuda de la tabla deuda
//
//            if ($totalDeudas >= $totalRepuestas) {
//                for ($i = 0; $i < $totalDeudas; $i++) {
//                    $elemento = $piezasConDeudaSinChasis[$i]->getTipoComponente();
//                    dump($elemento);
////                die();
//                    $piezasEnDeuda = $piezasEnDeuda . ' ' . $elemento;
//                };
//                $incidencia->setResumen(
//                    'Total de piezas con defectos:' . sizeof($piezasConDeudaSinChasis) . '   ' .
//                    'Piezas:' . $piezasEnDeuda
//                );
//                $em = $this->getDoctrine()->getManager();
//                $qb = $em->createQueryBuilder();
//                $query = $qb->delete('AppBundle:deuda', 't')
//                    ->where('t.cpu = :chasis')
//                    ->setParameter('chasis', $chasis)
//                    ->andWhere('t.tipoComponente =:tipo')
//                    ->setParameter('tipo', $elemento)
//                    ->getQuery();
//                $query->execute();
//                //  dump("entre aqui aaa");
//            } else {
//                //dump('h');
//
//                foreach ($piezasRepuestas as $pr) {
//                    $em = $this->getDoctrine()->getManager();
//                    $qb = $em->createQueryBuilder();
//                    $query = $qb->delete('AppBundle:deuda', 't')
//                        ->where('t.cpu = :chasis')
//                        ->setParameter('chasis', $chasis)
//                        ->andWhere('t.tipoComponente =:tipo')
//                        ->setParameter('tipo', $pr)
//                        ->getQuery();
//                    $query->execute();
//                }
//
//                if ($totalDeudas == 0) {
//                    $incidencia->setEstado('Solucionado');
//                    $incidencia->setResumen(
//                        'El inventario ' . $incidencia->getInventario() . ' tenia ' . $totalRepuestas . ' piezas con deudas las cuales han sido saldadas desde el taller'
//                    );
//                }
//
//            }
//
//
//        }
//        if ($totalDeudas == 0) {
//            $incidencia->setEstado('Solucionado');
//            $incidencia->setResumen(
//                'El inventario ' . $incidencia->getInventario() . ' tenia ' . $totalRepuestas . ' piezas con deudas las cuales han sido saldadas desde el taller'
//            );
//        }

        //  dump($piezasConDeuda);
        //    dump($totalDeudas); dump($totalRepuestas);
//        die();

//        if ($totalDeudas = 0) {
//            dump('igual');
//            $incidencia->setEstado('Solucionado');
//            $incidencia->setResumen(
//                'El inventario ' . $incidencia->getInventario() . ' tenia ' . $totalRepuestas . ' piezas con deudas las cuales han sido saldadas desde el taller'
//            );
//        }

        // dump($incidencia);
//        dump($incidencia_entregaPiezas);
//       // dump($piezasRepuestas);
//      //  dump($totalRepuestas);
//      //  dump($totalDeudas);
//         die();
        $entityManager->persist($incidencia);
        $entityManager->persist($incidencia_entregaPiezas);
        $entityManager->flush();
        return $this->redirectToRoute('incidencia_ver', ['id' => $incidencia_entregaPiezas->getId()]);


        //dump($piezasRepuestas);dump($incidencia_entregaPiezas);dump($incidencia);die();
        // return $this->render('estacion_trabajo/saldar_deuda_taller.html.twig', ['chasis' => $chasis[0], 'incidencia' => $incidencia,'componentes'=>$chasis[0]->getComponente()]);
    }

    /**
     * @Route("reportes/estacion/reporte_deudas_taller",name="imprimir_deudas_taller")
     */
    public function imprimirDeudastallerAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $lista = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipo=:tipo')
            ->setParameter('tipo', 'Pc defectuosa')
            ->andWhere('tabla.estado=:estadoIncidencia')
            ->setParameter('estadoIncidencia', 'Activa')
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery()->execute();

        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/reporte_deudas_taller.html.twig', ['lista' => $lista]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html), 200, array(

                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'page-size' => 'A4',

                'viewport-size' => '1280x1024',
                'margin-left' => '10mm',

                'margin-right' => '10mm',

                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }

    /**
     * @Route("reportes/estacion/reporte_entrega_taller",name="reporte_entrega_taller")
     */
    public function reporteEntregatallerAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:incidencia');
        $lista = $repository->createQueryBuilder('tabla')
            ->where('tabla.tipo=:tipo')
            ->setParameter('tipo', 'Pc defectuosa')
            ->andWhere('tabla.estado=:estadoIncidencia')
            ->setParameter('estadoIncidencia', 'Activa')
            ->orderBy('tabla.id', 'desc')
            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
            ->getQuery()->execute();

        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/reporte_deudas_taller.html.twig', ['lista' => $lista]);

        $filename = 'reporte';

        return new Response(

            $snappy->getOutputFromHtml($html), 200, array(

                'Content-Type' => 'application/pdf',
                'enable-javascript' => true,

                'page-size' => 'A4',

                'viewport-size' => '1280x1024',
                'margin-left' => '10mm',

                'margin-right' => '10mm',

                'margin-top' => '30mm',

                'margin-bottom' => '25mm',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'

            )

        );
    }

    /**
     * @Route("reportes/entrega_taller/", name="entrega_taller_reporte")
     */
    public function entregaTallerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $defaultData = array('message' => 'Type your message here');
        $form = $this->createForm('AppBundle\Form\entregaTallerFormType');

        $form->handleRequest($request);
        //   dump($form);die();
        if ($form->isValid()) {
            // data es un array con claves 'name', 'email', y 'message'
            $data = $form->getData();

            $fechaInicio = date_format($data['fechaInicio'], 'd-m-Y');
            $fechaFin = date_format($data['fechaFin'], 'd-m-Y');
            $lista = '';
            $tipoInfome = '';
            $deudas = $data['deudas'];
            $mant = $data['mantenimiento'];
            //  dump($data['fechaInicio']);            dump($data['fechaFin']);
            $qb = $em->createQueryBuilder();
            if ($deudas == true) {
                $tipo = "'Entrega de partes y piezas'";
                $qb->select(array('u'))// string 'u' is converted to array internally
                ->from('AppBundle:incidencia', 'u')
                    ->where(
                        $qb->expr()->eq('u.tipo', $tipo))
                    ->andWhere(
                        $qb->expr()->between('u.fecha', ':dateI', ':dateF')
                    )
                    ->setParameter('dateI', $data['fechaInicio'], \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('dateF', $data['fechaFin'], \Doctrine\DBAL\Types\Type::DATETIME)
                    ->orderBy('u.fecha', 'ASC');

                $lista = $qb->getQuery();
                //  dump($lista->getResult());die();
                $tipoInfome = "Entrega de partes y piezas";
                $snappy = $this->get('knp_snappy.pdf');
                //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
                $html = $this->renderView('deudas_taller/reporteEntregas.html.twig', ['form' => $form->createView(), 'fechaI' => $fechaInicio, 'fechaF' => $fechaFin, 'lista' => $lista->getResult(), 'tipoInforme' => $tipoInfome]);
                $filename = 'reporte';

            }
            if ($mant == true) {
                $tipo = "'Mantenimiento PC'";
                $qb->select(array('u'))// string 'u' is converted to array internally
                ->from('AppBundle:incidencia', 'u')
                    ->where($qb->expr()->orX(
                        $qb->expr()->eq('u.tipo', $tipo)
                    ))
                    ->andWhere(
                        $qb->expr()->between('u.fecha', ':dateI', ':dateF')
                    )
                    ->setParameter('dateI', $data['fechaInicio'], \Doctrine\DBAL\Types\Type::DATETIME)
                    ->setParameter('dateF', $data['fechaFin'], \Doctrine\DBAL\Types\Type::DATETIME)
                    ->orderBy('u.fecha', 'ASC');
                $lista = $qb->getQuery();
                $tipoInfome = "Mantenimiento PC";
            }
            $snappy = $this->get('knp_snappy.pdf');
            //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
            $html = $this->renderView('deudas_taller/reporteEntregas.html.twig', ['form' => $form->createView(), 'fechaI' => $fechaInicio, 'fechaF' => $fechaFin, 'lista' => $lista->getResult(), 'tipoInforme' => $tipoInfome]);
            $filename = 'reporte';
            //   dump($lista->getResult());die();
//            dump($lista->getResult());
//            die();
//            $piezasOriginales = substr($incidencia->getResumen(), 41);
//            $piezasOriginalesArray = explode(" ", $piezasOriginales);
//            $piezasOriginalesArray1 = explode(" ", $piezasOriginales);
////        dump($piezasOriginales);
//            dump($piezasOriginalesArray);
//            $total = sizeof($piezasRepuestas);
//            $piezasN = '';
//            for ($i = 0; $i < $total; $i++) {
//                $elemento = $piezasRepuestas[$i];
//                $piezasN = $piezasN . ' ' . $elemento;
//            };
//            $incidencia_entregaPiezas->setResumen(
//                'Total de piezas entregadas:' . sizeof($piezasRepuestas) . '   ' .
//                'Piezas:' . $piezasN
//            );
            return new Response(
                $snappy->getOutputFromHtml($html), 200, array(

                    'Content-Type' => 'application/pdf',
                    'enable-javascript' => true,

                    'page-size' => 'A4',

                    'viewport-size' => '1280x1024',
                    'margin-left' => '10mm',

                    'margin-right' => '10mm',

                    'margin-top' => '30mm',

                    'margin-bottom' => '25mm',
                    'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"'
                )
            );
            // return $this->render('deudas_taller/reporteEntregas.html.twig', ['form' => $form->createView(), 'fechaI' => $fechaInicio, 'fechaF' => $fechaFin, 'lista' => $lista->getResult(), 'tipoInforme' => $tipoInfome]);
        }

        return $this->render('reportes/reporte_entrega_taller.html.twig', ['form' => $form->createView()]);
    }

}


