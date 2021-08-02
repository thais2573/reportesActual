<?php

namespace AppBundle\Controller;

use AppBundle\Entity\solicitud;
use AppBundle\Entity\productoAssets;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class materialesController extends Controller
{
    /**
     * @Route("materiales/inicio",name="listado_materiales")
     *
     */
    public function listaMaterialesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usuario_actual = $this->getUser()->getUsername();
        if ($this->isGranted('ROLE_ADMIN')) {
            $lista = $em->getRepository('AppBundle:solicitud')->findAll();
        } else {
            $repository = $this->getDoctrine()
                ->getRepository('AppBundle:solicitud');
            $query = $repository->createQueryBuilder('tabla')
                ->where("tabla.solicitante = '$usuario_actual'")
//        ->andWhere("tabla.tipo !='$mov2'")
                ->orderBy('tabla.fechaSolicitud', 'desc')
//        ->distinct('!tabla.tipoEquipo', !'tabla.fecha', !'tabla.tipo=Edicion')
                // ->setParameters(array('numI', $numInv,'idE', $id_estacionT))
                ->getQuery();
            $lista = $query->getResult();
        }

        //  dump($usuario_actual);die();

        return $this->render('materiales/index.html.twig', ['listado' => $lista]);
    }

    /**
     * @Route("materiales/nueva_solicitud",name="nueva_solicitud")
     *
     */
    public function nuevaSolicitudAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $departamentos = $em->getRepository('AppBundle:departamento')->findAll();
        $productos = $this->cargarProductosAssetsAction();
        $solicitud = new solicitud();
        $zonaHoraria = new \DateTimeZone('America/Cuiaba');
        $fecha_actual = new \DateTime('now', $zonaHoraria);
        $solicitud->setFechaSolicitud($fecha_actual);
        $solicitud->setCodigo('1');
        $solicitudForm = $this->createForm('AppBundle\Form\materialesSForm', $solicitud);
        $solicitudForm->handleRequest($request);
        $mat = $em->getRepository('AppBundle:productoAssets')->getProductosEnExistencia();
      // dump($mat->getQuery()->execute());
        if ($solicitudForm->isSubmitted() && $solicitudForm->isValid()) {
            $datos_solicitud = $solicitudForm->getData();
            //$descripcion = $request->request->get('productos')[0];
            //$codigoProducto = $this->obtenerCodigoProductoAssetsAction($descripcion);
            // $codP = json_decode($codigoProducto->getContent(),true);
            // dump($datos_solicitud);
            foreach ($solicitud->getMaterial() as $item) {
                $item->setSolicitud($solicitud);
                //  $descripcion=$em->getRepository('AppBundle:productoAssets')->find($item->getId());

                // dump($item);

                // dump($this->getUser());die();
                $em->persist($item);
            }
            $consecutivo = $this->getUser()->getConsecutivoSolicitud();
            $solicitud->setNumeroSolicitud($consecutivo + 1);
            $solicitud->setSolicitante($this->getUser());
            $this->getUser()->setConsecutivoSolicitud($consecutivo + 1);
            //    dump($solicitud);die();
            //$codigoProd = $codP[0]['idP'];
            //$solicitud->setCodigo($codigoProd);
            //$solicitud->setMaterial($descripcion);
//      dump($codigoProd) ;
//      dump($codigoProducto);
//     // dump( $codigoProducto->{'id'});
//      dump($solicitud);
//      die();
            $em->persist($solicitud);
            $em->flush();

            return $this->redirectToRoute('ver_datos_solicitud', ['id' => $solicitud->getId()]);
            //die();
        }
//    dump($solicitudForm);
//    die();
        return $this->render('materiales/new.html.twig', [
            'solicitudForm' => $solicitudForm->createView(), 'dep' => $departamentos,
            'lista_productos' => $solicitud->getMaterial()]);
    }

    /**
     * @Route("materiales/{id}/ver", name="ver_datos_solicitud")
     * @param $id
     *
     * @return Response
     */
    public function showAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $personRepository = $entityManager->getRepository('AppBundle:solicitud');
        $solicitud = $personRepository->find($id);
        if ($solicitud == null) {
            throw $this->createNotFoundException("Incidencia no encontrada");
        }

        return $this->render('materiales/show.html.twig', ['solicitud' => $solicitud]);
    }

    /**
     * @Route("materiales/imprimir_solicitud/{id}",name="imprimir_solicitud")
     */
    public function imprimirSolicitudAction($id)
    {
        $lista = $this->getDoctrine()
            ->getRepository('AppBundle:solicitud')
            ->find($id);
        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/ficha_solicitud.html.twig', ['lista' => $lista]);

        $filename = 'reporteMateriales';

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
     * @Route("materiales/imprimir_valesolicitud/{id}",name="imprimir_valesolicitud")
     */
    public function imprimirvaleSolicitudAction($id)
    {
        $lista = $this->getDoctrine()
            ->getRepository('AppBundle:solicitud')
            ->find($id);
        $snappy = $this->get('knp_snappy.pdf');
        //$snappy->setOption('user-style-sheet', 'http://localhost/build/css/estilos-tabla-taller.css');
        $html = $this->renderView('reportes/valeSolicitud.html.twig', ['lista' => $lista]);

        $filename = 'reporteMateriales';

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
                    WHERE e.Existencia_Actual > 0";

            $query = $coneccionAssets->query($sql);
            if ($query) {

                $result = array();
                //  dump($result);die();
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
            }
        } catch (\PDOException $e) {
            echo 'No se conecta con el servidor! - ' . $e->getMessage();
        }


        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);


        // return $result;

    }

    /**
     * @Route("reportes/codigo_producto/{descripcion}",name="codigo_producto_assets")
     */
    public function obtenerCodigoProductoAssetsAction($descripcion)
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

            $sql = "SELECT p.Id_Producto as id,p.Desc_Producto as descripcion FROM Producto as p 
                    WHERE p.Desc_Producto='" . $descripcion . "'";
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
                        "idP" => $r['id']
                    );
                }
                return new JsonResponse($responseArray);
            }
        } catch (\PDOException $e) {
            echo 'No se conecta con el servidor! - ' . $e->getMessage();
        }


//          dump($responseArray);die();
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse($responseArray);


        // return $result;

    }

    /**
     * @Route("reportes/existencia_productos/",name="existencia_productos_assets")
     *
     * @param Request $request
     * @Method("GET")
     *
     * @return JsonResponse
     *
     */
    public function rexistenciaProductosAssetsAction(Request $request)
    {
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        //$idProducto=0;

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
            $id_producto = $request->query->get('id_producto');
            $entityManager = $this->getDoctrine()->getManager();
            $id_productoEnAssets = $entityManager->getRepository('AppBundle:productoAssets')->find($id_producto)->getIdProducto();
            // $result = mssql_query("SELECT Existencia_Actual AS existencia FROM [dbo].[Existencia] WHERE Id_Producto='".$cond."';", $coneccionPremium);
            $sql = "SELECT e.Existencia_Actual as total,e.Id_Producto as id  FROM  Existencia as e 
                    WHERE  e.Existencia_Actual>0 and e.Id_Producto='" . $id_productoEnAssets . "'";
            $query = $coneccionAssets->query($sql);
            //dump($query);die();
            if ($query) {
                $result = array();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $result[] = $var;
                }
                $responseArray = array();
                foreach ($result as $r) {
                    $responseArray[] = array(
                        "total" => $r['total'],
                        "id_producto" => $r['id']
                    );
                }
                //  dump($responseArray);die();
                return new JsonResponse($responseArray);
            }
        } catch (\PDOException $e) {
            echo 'No se conecta con el servidor! - ' . $e->getMessage();
        }
        //dump($responseArray);die();
        // Return array with structure of the neighborhoods of the providen city id
        return new JsonResponse(null);

    }

    /**
     * @Route("reportes/actualizar_productos", name="actualizar_productos")
     */
    public function actualziarProductosAction(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        ///Obtener departamentos
        /**
         * Configurar conexion al assets desde Windows (Liuben)
         */
        $serverName = "premium.cicc.cu";
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
             * Consulta para obtener los centros de costos
             */
            // $sql = "SELECT cc.IdProducto as idP,cc.Des_Producto,cc.Fecha_Entrada,cc.UM_Almacen FROM Producto cc";
            $sql = "SELECT p.Id_Producto as id,p.Desc_Producto  as descripcion,p.Fecha_Entrada as fecha,p.UM_Almacen as um ,e.Existencia_Actual as exist FROM Producto as p INNER JOIN Existencia as e ON p.Id_Producto=e.Id_Producto
                    WHERE e.Existencia_Actual>0";
            $query = $coneccionAssets->query($sql);
//      dump($query);die();
            // $idcosto = 0;
            //$departamento='';
            if ($query) {
                $resultados = array();
                $em = $this->getDoctrine()->getManager();
                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
                    $resultados[] = $var;
                }

                foreach ($resultados as $a) {
                    $entity = new productoAssets();
                    $idproducto = $a['id'];
                    $descripcion = $a['descripcion'];
                    $fechaEntrada = $a['fecha'];
                    $umAlmacen = $a['um'];
                    $existencia = $a['exist'];

                    set_time_limit(0);
                    $entity->setIdProducto($idproducto);
                    $entity->setDescripcion($descripcion);
                    $entity->setFechaEntrada($fechaEntrada);
                    $entity->setUmAlmacen($umAlmacen);
                    $entity->setExistencia($existencia);

                    /* if(mssql_num_rows($entity->)>0){
                       break;
                     }*/
                    $entityManager = $this->getDoctrine()->getManager();
                    $productoExiste = $entityManager->getRepository('AppBundle:productoAssets')->findOneBy(['idProducto' => $idproducto]);

                   if ($productoExiste == null) {
                        $em->persist($entity);
                        $em->flush();
                    }
                   else if($productoExiste && $productoExiste->getExistencia()!=$existencia){
                    //   dump($productoExiste);dump($existencia);die();
                       $entity->setExistencia($existencia);
                       $em->flush();
                   }
                }
                $lista_productos = $entityManager->getRepository('AppBundle:productoAssets')->findAll();
                $lista_solicitudes = $entityManager->getRepository('AppBundle:solicitud')->findAll();
                //dump($lista_productos);die();
            }
            //  $cant=0;

            //   $cant=$cant+1;
            // dump($dep);die();
        } catch (\PDOException $e) {
            die("No se conecta con el servidor! - " . $e->getMessage());
        }
        // dump($result[0]['Desc_Ccosto']);die();


        return $this->redirectToRoute('nueva_solicitud');

    }

    /**
     * @Route("reportes/materiales/editar_solicitud/{id}", name="editar_solicitud_materiales")
     */
    public function editarSolicitudMaterialesAction(Request $request, $id)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $departamentos = $entityManager->getRepository('AppBundle:departamento')->findAll();
        $solicitud = $entityManager->getRepository('AppBundle:solicitud')->find($id);


        $solicitudForm = $this->createForm(\AppBundle\Form\materialesSForm::class, $solicitud);
       // $productoSolicitado=$entityManager->getRepository('AppBundle:productoSolicitado')->findBy(['solicitud'=>$solicitud->getId()]);
       // dump($solicitud->getMaterial()[0]);die();
        $solicitudForm->handleRequest($request);
        $mat = $entityManager->getRepository('AppBundle:productoAssets')->getAllProductos();
        //  dump($mat->getQuery()->fetchAll());
        if ($solicitudForm->isSubmitted() && $solicitudForm->isValid()) {
            $datos_solicitud = $solicitudForm->getData();
//               dump($solicitudForm);die();
            //$descripcion = $request->request->get('productos')[0];
            //$codigoProducto = $this->obtenerCodigoProductoAssetsAction($descripcion);
            // $codP = json_decode($codigoProducto->getContent(),true);
            // dump($datos_solicitud);
            foreach ($datos_solicitud->getMaterial() as $item) {
                $item->setSolicitud($datos_solicitud);
                //  $descripcion=$em->getRepository('AppBundle:productoAssets')->find($item->getId());

                // dump($item);
                $entityManager->persist($item);
            }
            $datos_solicitud->setSolicitante($this->getUser());
            // dump($solicitud);die();
            //$codigoProd = $codP[0]['idP'];
            //$solicitud->setCodigo($codigoProd);
            //$solicitud->setMaterial($descripcion);
//      dump($codigoProd) ;
//      dump($codigoProducto);
//     // dump( $codigoProducto->{'id'});
//            dump($datos_solicitud);
//      dump($solicitud);
//      die();
            $entityManager->persist($datos_solicitud);
            $entityManager->flush();

            return $this->redirectToRoute('ver_datos_solicitud', ['id' => $datos_solicitud->getId()]);
            //die();
        }
//    dump($solicitudForm);
//    die();
        return $this->render('materiales/new.html.twig', [
            'solicitudForm' => $solicitudForm->createView(), 'dep' => $departamentos,
            'lista_productos' => $solicitud->getMaterial()]);
    }

}

