<?php

namespace AppBundle\Controller;

use AppBundle\Entity\area;
use AppBundle\Entity\equipo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class conexionassetsController extends Controller
{
  /**
   * @Route("reportes/conexion1",name="exito")
   */
  public function indexAction()
  {
    return $this->render('prueba/exito.html.twig');
  }


  /**
   * @Route("reportes/lista_departamentos_areas", name="lista_departamentos_areas")
   * @Method("GET")
   * @return Response
   */
  public function list_depAction()
  {


    $entityManager = $this->getDoctrine()->getManager();
    $applicationRepository = $entityManager->getRepository('AppBundle:area')->findAll();
    $applicationRepository2 = $entityManager->getRepository('AppBundle:departamento')->findAll();

    return $this->render(
      'tipo/list_departamentos.html.twig',
      [
        'lista' => $applicationRepository,
        'dep' => $applicationRepository2
      ]
    );
  }

  /**
   * @Route("reportes/lista_equipos_assets", name="lista_equipos_assets")
   * @Method("GET")
   * @return Response
   */
  public function list_EsquiposAction(Request $request)
  {


    $entityManager = $this->getDoctrine()->getManager();
    $applicationRepository = $entityManager->getRepository('AppBundle:equipoAssets')->findAll();


    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
      $applicationRepository,
      $request->query->getInt('page', 1),
      13
    );

    return $this->render(
      'perifericos/equipos_asset.html.twig',
      [
        'pagination' => $pagination
      ]
    );
  }

  /**
   * @Route("/reportes/equipos/filtrar_equipos_assets",name="filtra_assets")
   */
  public function filtraIncidAction(Request $request)
  {


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
      // dump($applicationRepository);
      // die();
      $dql = "SELECT a.id,a.descripcion, a.numInventario,a.id_costo as IdCosto,a.id_area as idArea,a.activo,a.serie FROM AppBundle:equipoAssets a WHERE a.numInventario = " . $num . "";
      $query = $em->createQuery($dql);
      // dump($applicationRepository);
      //  die();
      $paginator = $this->get('knp_paginator');
      $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), 1
      );
      // dump( $applicationRepository->getId());
      //  die();
      // dump($estacion);
      //  die();
      return $this->render('perifericos/equipos_asset.html.twig', ['pagination' => $pagination]);
    } else

      $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
    return $this->redirectToRoute('lista_equipos_assets');
  }

  /**
   * @Route("/reportes/equipos/filtrar_equipos_assets2/{dep}",name="filtra_assets2")
   */
  public function filtraAssets2Action(Request $request, $dep)
  {    if ($request->request == null) {
    $nombre_departamento = $request->query->get('usuarios')[0];
  //  dump($this->get('aquii'));
  } else {
    $nombre_departamento = $request->request->get('usuarios')[0];
  }
//        $usuarios=new  ArrayCollection();
//        $usuarios[0]=$nombre_departamento;

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
      //$applicationRepository = $entityManager->getRepository('AppBundle:equipoAssets')->findBy(['numInventario' => $num]);
//dump($num);die();
//        $repository = $this->getDoctrine()
//            ->getRepository('AppBundle:equipoAssets');
//        $applicationRepository = $repository->createQueryBuilder('tabla')
//            ->where('tabla.numInventario LIKE :numero')
//         //   ->andWhere('tabla.inventario =: idE')
//            ->setParameter('numero', $num)
////            ->andWhere('tabla.tipoEquipo = :asesorio')
////            ->setParameter('asesorio', 'cpuchasis')
//            // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
//            ->getQuery()
//            ->getResult();

        $qb = $em->createQueryBuilder();
        $result = $qb->select('t')->from('AppBundle\Entity\equipoAssets', 't')
            ->where( $qb->expr()->like('t.numInventario', $qb->expr()->literal('%' . $num . '%')) )
            ->getQuery();
         //   ->getResult();

     //   dump($result);die();
      //  $estacion = $applicationRepository;
      $em = $this->get('doctrine.orm.entity_manager');
//             dump($applicationRepository);
//             die();
      $dql = "SELECT a FROM AppBundle:equipoAssets a WHERE a.numInventario = " . $num . " ";
      $query = $em->createQuery($dql);
//       dump($dql);
//        die();
        if($query->execute()==null){
            $this->addFlash('alert', 'No existe equipo con el numero de inventario especificado');
        }else{
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $result,
                $request->query->getInt('page', 1)
            );
            $are = $entityManager->getRepository('AppBundle:area')->findOneBy(['nombre' => $dep]);

            return $this->render('activos_fijos/lista_activos_fijos.html.twig', ['pagination' => $pagination, 'areas' => $area, 'componente' => 'backup',
                'inventarios' => $accesorio, 'dep' => $dep,'area'=>$are,'centros'=>'','dir'=>'','orden'=>'','jsonList'=>'',
                'lista' => $result->getResult()]);

        }}


    return $this->redirectToRoute('lista_activos_fijos');
  }


  /**
   * @Route("reportes/conexion",name="lista_areas_costo")
   */
  public function conectarAction()
  {
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
      if ($query) {
        $result = array();
        while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
          $result[] = $var;
        }
        // $em = $this->getDoctrine()->getManager();
        // $entity = new area();
        foreach ($result as $centro) {
          dump($centro['Desc_Ccosto']);

        }

        /**
         * Consulta para obtener los departamentos segun el centro de costo al que pertenesca
         */
        $id_ccosto = '003';
        $sql = "SELECT ar.Desc_AreaResponsabilidad FROM dbo.Areas_Responsabilidad ar WHERE ar.Id_Ccosto = '$id_ccosto'";
        $query = $coneccionAssets->query($sql);
        if ($query) {
          $result = array();
          while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $var;
          }
          foreach ($result as $depart) {
            dump($depart['Desc_AreaResponsabilidad']);
          }
        }
      }
      dump('entrandoooo yeaaaaaa');
      die();
    } catch (\PDOException $e) {
      die("No se conecta con el servidor! - " . $e->getMessage());
    }


    // return $result;
  }
  /**
   * @Route("reportes/cargar_productos",name="cargar_productos_assets")
   */
  public function cargarProductosAssetsAction()
  {
    /**
     * Configurar conexion al assets desde Windows (Liuben)
     */
    $serverName = "192.168.107.20";
    $database = "retinosis";
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
       * Consulta para obtener los productos
       */

      $sql = "SELECT p.Id_Producto as id,p.Desc_Producto as descripcion FROM Producto as p INNER JOIN Existencia as e ON p.Id_Producto=e.Id_Producto
                    WHERE e.Existencia_Actual>0";
      $query = $coneccionAssets->query($sql);
      if ($query) {
        $result = array();
        while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
          $result[] = $var;
        }

        // $em = $this->getDoctrine()->getManager();
        // $entity = new area();
          $responseArray = array();
          foreach ($result as $r) {
              $responseArray[] = array(
                  "id" => $r['id'],
                  "descripcion" => $r['descripcion']
              );
          }
//          dump($responseArray);die();
          // Return array with structure of the neighborhoods of the providen city id
          return new JsonResponse($responseArray);
        /**
         * Consulta para obtener los departamentos segun el centro de costo al que pertenesca
         */
        $id_ccosto = '003';
        $sql = "SELECT ar.Desc_AreaResponsabilidad FROM dbo.Areas_Responsabilidad ar WHERE ar.Id_Ccosto = '$id_ccosto'";
        $query = $coneccionAssets->query($sql);
        if ($query) {
          $result = array();
          while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
            $result[] = $var;
          }
          foreach ($result as $depart) {
            dump($depart['Desc_AreaResponsabilidad']);
          }
        }
      }
      dump('entrandoooo yeaaaaaa');
      die();
    } catch (\PDOException $e) {
      die("No se conecta con el servidor! - " . $e->getMessage());
    }


    // return $result;
  }

  /**
   * @Route("/reportes/departamentos1/{id_ccosto}",name="lista_departamentos1")
   */
  public function listaDepartamentosAction($id_ccosto)
  {
    /**
     * Configuracion para realizar el metodo por linux
     * Inicio de escritura de procederes para la bd del assets
     *
     * try {
     * $coneccionAssets = new \PDO("sqlsrv:Server = tcp:192.168.107.20; Database = retinosis", "user_assetsp", "2020*Fuerza");
     * /**
     * En esta consulta se agrega la variable $nombre y la columna nombre en caso de que se desee pasar el nombre a la bd del assets
     *
     * //$id = (int) $id;
     * $sql = "SELECT Id_Departamento FROM CentroCosto";
     * /**
     * Consulta y obtencion de datos
     *
     * $query = $coneccionAssets->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY, \PDO::SQLSRV_ATTR_QUERY_TIMEOUT => 1));
     * $query->execute($sql);
     * //      //$sql = "INSERT INTO dbo.Procederes (Procederes_Id, Fecha, Estado, Pacientes_id, Habitacion, Pais, Nombre) VALUES($id, '".$fecha."', 3, $hc, 0, '".$pais."', '".$nombre."')";
     *
     * //  $query = $coneccionAssets->query($sql);
     * if($query) {
     * $result = array();
     * dump($result);
     * die();
     * while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
     * $result[] = $var;
     * }
     * }
     *
     * /**
     * Cierro conexion con el assets
     *
     * $coneccionAssets = null;
     *
     * } catch (\PDOException $e) {
     * echo 'No se conecta con el servidor! - ' . $e->getMessage();
     * }
     *
     *
     * return $this->redirectToRoute('exito');*/

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
       * Consulta para obtener los departamentos segun el centro de costo al que pertenesca
       */
      //  $id_ccosto = '003';
      $sql = "SELECT ar.Desc_AreaResponsabilidad FROM dbo.Areas_Responsabilidad ar WHERE ar.Id_Ccosto = '$id_ccosto'";
      $query = $coneccionAssets->query($sql);
      if ($query) {
        $result = array();
        while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
          $result[] = $var;
        }
        foreach ($result as $depart) {
          dump($depart['Desc_AreaResponsabilidad']);
        }
      }

      ///  dump('entrandoooo yeaaaaaa');
      // die();
    } catch (\PDOException $e) {
      die("No se conecta con el servidor! - " . $e->getMessage());
    }
  }


  /**
   * Returns a JSON string with the neighborhoods of the City with the providen id.
   * @Route("/reportes/departamentos",name="list_departamentos")
   *
   * @param Request $request
   * @Method("GET")
   *
   * @return JsonResponse
   */
  public function listNeighborhoodsOfCityAction(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $id_ccosto = $request->query->get("id_ccosto");
    //dump($id_ccosto);die();
    $area = $entityManager->getRepository('AppBundle:area')->findOneBy(['id_area' => $id_ccosto]);
    $departamentos = $entityManager->getRepository('AppBundle:departamento')->findBy(['area' => $area]);
    //dump($area);die();
    $responseArray = array();
    foreach ($departamentos as $depart) {
      $responseArray[] = array(
        "dep" => $depart->getName(),
        "id_dep" => $depart->getIdCosto(),
        "id"=>$depart->getId(),
        "id_area" => $area->getId());
    }
//dump($responseArray);die();
    // Return array with structure of the neighborhoods of the providen city id
    return new JsonResponse($responseArray);
    // dump($responseArray);die();
    // e.g
    // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
  }


  /**
   * Returns a JSON string with the neighborhoods of the City with the providen id.
   * @Route("reportes/list_inventarios",name="list_inventarios")
   *
   * @param Request $request
   * @Method("GET")
   *
   * @return JsonResponse
   */
  public function listunidadesOfMunicipiosAction(Request $request)
  {
    // Get Entity manager and repository
    $em = $this->getDoctrine()->getManager();
    $neighborhoodsRepository = $em->getRepository("AppBundle:inventario");

    // dump( $request->query->get("id_Costo2"));die();
    $id_de = trim($request->query->get("id_Costo2"));
    $idArea = $request->query->get('idArea');
    //$departamento = $em->getRepository("AppBundle:departamento")->findBy(['idCosto' => $id_de]);

    $repository = $this->getDoctrine()
      ->getRepository('AppBundle:departamento');
    $query = $repository->createQueryBuilder('tabla')
      ->where('tabla.area = :Are')
      ->setParameter('Are', $idArea)
      ->andWhere('tabla.idCosto =:costo')
      ->setParameter('costo', $id_de)
      ->getQuery();
    $departamento = $query->getResult();

    $neighborhoods = $neighborhoodsRepository->createQueryBuilder("q")
      ->where("q.centroCosto = :id_Costo")
      ->setParameter("id_Costo", $departamento[0]->getId())
      ->andWhere("q.estado =:estado")
      ->setParameter("estado", 'Activo')
      ->getQuery()
      ->getResult();
//    dump($departamento);dump($id_de);dump($neighborhoods);
//    die();
    $responseArray = array();
    foreach ($neighborhoods as $neighborhood) {
      $responseArray[] = array(
        "dep" => $neighborhood->getcentroCosto()->getName(),
        "id_estacion" => $neighborhood->getId(),
        "nombreRed" => $neighborhood->getNombreRed()
      , "centroCosto" => $neighborhood->getCentroCosto()
      );
    }
//     dump($neighborhood->getCentroCosto());
//    die();
    // Return array with structure of the neighborhoods of the providen city id
    return new JsonResponse($responseArray);

    // e.g
    // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
  }
 /**
   * Returns a JSON string with the neighborhoods of the City with the providen id.
   * @Route("reportes/list_inventarios_editar_equipo",name="list_inventarios_editar_equipo")
   *
   * @param Request $request
   * @Method("GET")
   *
   * @return JsonResponse
   */
  public function listInventariosEditEquipoAction(Request $request)
  {
    // Get Entity manager and repository
    $em = $this->getDoctrine()->getManager();
    $neighborhoodsRepository = $em->getRepository("AppBundle:inventario");

   // dump( $request);die();
    $id_de = trim($request->query->get("id_departamento"));
    //$departamento = $em->getRepository("AppBundle:departamento")->findBy(['idCosto' => $id_de]);

    $repository = $this->getDoctrine()
      ->getRepository('AppBundle:departamento');
    $query = $repository->createQueryBuilder('tabla')
      ->where('tabla.id = :Are')
      ->setParameter('Are', $id_de)
      ->getQuery();
    $departamento = $query->getResult();
//    dump($departamento);dump($id_de);
//    die();
    $neighborhoods = $neighborhoodsRepository->createQueryBuilder("q")
      ->where("q.centroCosto = :centro")
      ->setParameter("centro", $departamento[0]->getId())
      ->andWhere("q.estado =:estado")
      ->setParameter("estado", 'Activo')
      ->getQuery()
      ->getResult();
    $responseArray = array();
//        dump($neighborhoods);dump($departamento);
//         die();
    foreach ($neighborhoods as $neighborhood) {
      $responseArray[] = array(
        "dep" => $neighborhood->getcentroCosto()->getName(),
        "id_estacion" => $neighborhood->getId(),
        "nombreRed" => $neighborhood->getNombreRed()
      , "centroCosto" => $neighborhood->getCentroCosto()
      );
    }
//     dump($neighborhood->getCentroCosto());
//    die();
    // Return array with structure of the neighborhoods of the providen city id
    return new JsonResponse($responseArray);

    // e.g
    // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
  }

  /**
   * Returns a JSON string with the neighborhoods of the City with the providen id.
   * @Route("reportes/list_equipos_inventario",name="list_equipos_inventario")
   *
   * @param Request $request
   * @Method("GET")
   *
   * @return JsonResponse
   */
  public function list_equipos_inventarioAction(Request $request)
  {
    // Get Entity manager and repository
    $em = $this->getDoctrine()->getManager();
    $neighborhoodsRepository = $em->getRepository("AppBundle:equipo");

    $id_de = trim($request->query->get("id_Costo2"));
    $inv = $em->getRepository("AppBundle:inventario")->find($id_de);
    $departamento = $em->getRepository("AppBundle:equipo")->findBy(['estacion' => $inv]);
    // dump($departamento[0]->getIdCosto());die();
    // Search the neighborhoods that belongs to the city with the given id as GET parameter "cityid"
    $cantE = $em->getRepository('AppBundle:equipo')->CantidadEquipos($inv->getId());
    $cantM = 0;
    $cantBackup = 0;
    $cantImpresora = 0;
    $cantEst = 0;
    $cantScan = 0;
    $cantChasis = 0;
    $responseArray = array();
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

      $responseArray[] = array(
        "equipo" => $cant['tipoEquipo'],
        "cantidad" => $cant['1']
      );
    }
    //dump( $neighborhoods);die();
    // Serialize into an array the data that we need, in this case only name and id
    // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
    $responseArray = array();

    return new JsonResponse($responseArray);


  }/**
   * Returns a JSON string with the neighborhoods of the City with the providen id.
   * @Route("reportes/lista_chasis",name="lista_chasis_sin_asignar")
   *
   * @param Request $request
   * @Method("GET")
   *
   * @return JsonResponse
   */
  public function listaChasisSinEstacionAction(Request $request)
  {
    // Get Entity manager and repository
    $em = $this->getDoctrine()->getManager();
      $repositoryChasis = $this->getDoctrine()
          ->getRepository('AppBundle:equipo');

      $query = $repositoryChasis->createQueryBuilder('tabla')
          ->where('tabla.tipoEquipo = :tipo')
          // ->andWhere('tabla.inventario =: idE')
          ->setParameter('tipo', 'cpuchasis')
          ->orderBy('tabla.id', 'DESC')
          // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
          ->getQuery();
      $listaChasis = $query->getResult();

      dump($listaChasis);die();
      $responseArray[] = array(
        "equipo" => $listaChasis['tipoEquipo'],
        "cantidad" => $listaChasis['1']
      );

    //dump( $neighborhoods);die();
    // Serialize into an array the data that we need, in this case only name and id
    // Note: you can use a serializer as well, for explanation purposes, we'll do it manually
    $responseArray = array();

    return new JsonResponse($responseArray);


  }

  /**
   * @Route("reportes/activos_fijos/adicionar",name="adicionar_a_equipos")
   */
  public function adicionaraEquipoAEstacion(Request $request)
  {
    $entityManager = $this->getDoctrine()->getManager();
    $id_equipo = $request->query->get('id');
    $tipo = $request->request->get('tipo');
    $equipo = $entityManager->getRepository('AppBundle:equipoAssets')->find($id_equipo);
//    dump($equipo->getNumInventario());
//    die();
    $nI = $equipo->getnumInventario();
    $desc = $equipo->getDescripcion();
    $serie = $equipo->getSerie();

    $equipo_nuevo = new equipo();
    $equipo_nuevo->setNumInventario($nI);
    $equipo_nuevo->setModelo($desc);
    $equipo_nuevo->setSerie($serie);
    $equipo_nuevo->setTipoEquipo($tipo);
    //dump($equipo_nuevo);die();
    $entityManager->persist($equipo_nuevo);
    $entityManager->flush();
    // $equipo = $entityManager->getRepository('AppBundle:equipo')->findBy(['nu']$id_equipo);
    return $this->redirectToRoute('ver_datos_periferico', ['id' => $equipo_nuevo->getId()]);

  }
//  /**
//   * Returns a JSON string with the neighborhoods of the City with the providen id.
//   * @Route("/reportes/equipos",name="adicionar_a_equipos")
//   *
//   * @param Request $request
//   * @param $tipo_equipo
//   * @Method("GET")
//   *
//   * @return JsonResponse
//   */
//  public function adicionaraEquipoA(Request $request)
//  {
////
//    $entityManager = $this->getDoctrine()->getManager();
//    $id_ccosto = $request->query->get("id_ccosto");
//    // dump($id_ccosto);die();
//    $entityManager = $this->getDoctrine()->getManager();
//    dump($request);
//    die();
//    $equipo = $entityManager->getRepository('AppBundle:equipoAssets')->find($id);
//
//    $responseArray = array();
//    foreach ($departamentos as $depart) {
//      $responseArray[] = array(
//        "dep" => $depart->getName(),
//        "id_dep" => $depart->getIdCosto());
//    }
//
//    // Return array with structure of the neighborhoods of the providen city id
//    return new JsonResponse($responseArray);
//    // dump($responseArray);die();
//    // e.g
//    // [{"id":"3","name":"Treasure Island"},{"id":"4","name":"Presidio of San Francisco"}]
//  }
}
