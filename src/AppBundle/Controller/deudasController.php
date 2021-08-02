<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class deudasController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }

    /**
     * Matches /blog exactly
     *
     * @Route("reportes/eliminarDeuda/{idChasis}", name="eliminarDeuda")
     * @return Response
     */
    public function eliminarDeudaAction(Request $request, $idChasis)
    {
        $equipo = $this->getDoctrine()->getRepository('AppBundle:equipo')->findBy(['id' => $idChasis]);
        $entityManager2 = $this->getDoctrine()->getManager();


//dump($equipo);die();
//        $equipo[0]->setEstacion(null);
//        $equipo[0]->setEstado('Activo');
//        $entityManager2->persist($equipo[0]);
//        $entityManager2->flush();

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb2 = $em->createQueryBuilder();
        $query = $qb->delete('AppBundle:deuda', 't')
            ->where('t.cpu = :numIn')
            ->setParameter('numIn', $equipo[0])
            ->getQuery();
        $query->execute();
        $query2 = $qb2->delete('AppBundle:incidencia', 't')
            ->where('t.idE = :id')
            ->setParameter('id', $equipo[0]->getId())
            ->getQuery();
        $query2->execute();
//        dump($query2->execute());
//        die();

        $this->addFlash('mensaje', 'La deuda del chasis con inventario '.$equipo[0]->getNumInventario()." ha sido eliminada");
        return $this->redirectToRoute('lista_deudas_taller');
    }


}
