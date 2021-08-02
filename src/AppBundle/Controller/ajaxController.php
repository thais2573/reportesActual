<?php

namespace AppBundle\Controller;
use AppBundle\Entity\equipo;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ajaxController extends Controller
{
    /**
     * Returns a JSON string with the neighborhoods of the City with the providen id.
     * @Route("/reportes/existeComponente",name="existeComponente")
     *
     * @param Request $request
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function existeComponente(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $serie = $request->query->get("serie");

        $componente = $entityManager->getRepository('AppBundle:componente')->findOneBy(['serie' => $serie]);

        $responseArray = array();
        if($componente){
            $responseArray[] = array(
                "id"=>$componente->getId());
        }
        return new JsonResponse($responseArray);
    }
    /**
     * @Route("/reportes/guardaEquipoAjax",name="guardaEquipoAjax")
     *@param Request $request
     * @Method("GET")
     *
     * @return JsonResponse
     */
    public function asignaEquipoEstacion(Request $request){

       $arrayEquipos=$request->get('equipos');
        $entityManager = $this->getDoctrine()->getManager();
        $estacion=$entityManager->getRepository('AppBundle:inventario')->find($request->get('estacionId'));
        $dep = $entityManager->getRepository('AppBundle\Entity\departamento')->findBy(['id' => $estacion->getCentroCosto()])[0];
        //dump($dep);
        //die();
       foreach($arrayEquipos as $e){
           $equipo=$entityManager->getRepository('AppBundle:equipo')->findBy(['numInventario'=>$e['numI']])[0];
           $equipo->setEstacion($estacion);
           $equipo->setEstado('Activo');
           $equipo->setDepartamento($dep);
           $estacion->addEquipo($equipo);
           $entityManager->persist($equipo);
          // dump($equipo);die();
       }
       $entityManager->flush();
    }
}
