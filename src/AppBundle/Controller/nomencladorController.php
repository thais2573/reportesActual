<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class nomencladorController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * @Route("reportes/toner/list", name="lista_toner")
     * @Method("GET")
     * @return Response|\Symfony\Component\HttpFoundation\Response
     */
    public function listaTonerAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $toners = $entityManager->getRepository('AppBundle:toner')->findAll();

        return $this->render(
            'toner/listado.html.twig',
            [
                'lista'=>$toners
            ]
        );
    }
    /**
     * @Route("reportes/toner/new", name="nuevo_toner")
     */
    public function newTonerAction(Request $request)
    {
        $tonerForm = $this->createForm('AppBundle\Form\tonerType');
        /**
         *Star "Post only" section
         */
        $tonerForm->handleRequest($request);
        if ($tonerForm->isSubmitted() && $tonerForm->isValid())
        {

            $tipo = $tonerForm->getData();

            $entityManager = $this->getDoctrine()->getManager();



            $entityManager->persist($tipo);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría Creada Correctamente');
            return $this->redirectToRoute('lista_toner');
        }
        return $this->render('toner/new.html.twig', ['form' => $tonerForm->createView()]);

    }
    /**
     * @Route("reportes/existencia_toner/",name="existencia_toner")
     *
     * @param Request $request
     * @Method("GET")
     *
     * @return JsonResponse
     *
     */
    public function existenciaTonerAssetsAction(Request $request)
    {
            $modelo = $request->query->get('modelo');
            $entityManager = $this->getDoctrine()->getManager();
            $toner = $entityManager->getRepository('AppBundle:toner')->findBy(['modelo'=>$modelo]);
            // $result = mssql_query("SELECT Existencia_Actual AS existencia FROM [dbo].[Existencia] WHERE Id_Producto='".$cond."';", $coneccionPremium);
//            $sql = "SELECT e.Existencia_Actual as total,e.Id_Producto as id  FROM  Existencia as e
//                    WHERE  e.Existencia_Actual>0 and e.Id_Producto='" . $id_productoEnAssets . "'";
//            $query = $coneccionAssets->query($sql);
            //dump($query);die();
//            if ($toner) {
//                $result = array();
//                while ($var = $query->fetch(\PDO::FETCH_ASSOC)) {
//                    $result[] = $var;
//                }
                $responseArray = array();
                foreach ($toner as $r) {
                    $responseArray[] = array(
                        "total" => $r->getCantidad(),
                        "id_producto" => $r->getId()
                    );
                }
//                dump($request);
//                dump($responseArray);die();
                return new JsonResponse($responseArray);
            }
}
