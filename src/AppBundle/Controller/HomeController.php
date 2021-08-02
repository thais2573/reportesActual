<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
  /**
   * @Route("/reportes",name="control_reportes")
   */
  public function homeAction()
  {
    $em = $this->getDoctrine()->getManager();

    $entaller = $em->getRepository('AppBundle:taller')->findAll();
    $totalTaller = count($entaller);
    $almacen = $this->getDoctrine()
      ->getRepository('AppBundle:inventario')->find(3861);
    $enAlmacen = $this->getDoctrine()
      ->getRepository('AppBundle:equipo')
      ->findBy(['estacion' => $almacen]);
    $totalAlmacen = count($enAlmacen);
    $incidenciasEnProceso = $this->getDoctrine()
      ->getRepository('AppBundle:incidencia')
      ->findBy(['estado' => 'En Proceso']);
    $totalEnProceso = count($incidenciasEnProceso);
    $incidenciasEnReparacion = $this->getDoctrine()
      ->getRepository('AppBundle:incidencia')
      ->findBy(['estado' => 'Reparacion']);
    $totalEnReparacion = count($incidenciasEnReparacion);
//    dump($totalEnProceso);
//    die();
    return $this->render('home/index.html.twig', ['totalTaller' => $totalTaller, 'totalAlmacen' => $totalAlmacen,'totalEnProceso'=>$totalEnProceso,'enReparacion'=>$totalEnReparacion]);
  }
}
