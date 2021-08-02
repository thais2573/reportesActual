<?php

namespace AppBundle\Controller;

use DH\DoctrineAuditBundle\Reader\AuditReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class trazasController extends Controller
{
  public function indexAction($name)
  {
    return $this->render('', array('name' => $name));
  }
  /**
   * @Route("/audit/details/{entity}/{id}", name="dh_doctrine_audit_show_audit_entry", methods={"GET"})
   */
  public function showAuditEntryAction($entity,$id)
  {
    $reader = $this->container->get('dh_doctrine_audit.reader');

    $data = $reader
      ->filterBy(AuditReader::UPDATE)   // add this to only get `update` entries.
      ->getAudit($entity, $id)
    ;

    return $this->render('administracion/historial/trazas.html.twig', [
      'entity' => $entity,
      'entry' => $data[0],
    ]);
  }
  /**
   * @Route("reportes/listado_trazas", name="listado_trazas")
   *
   */
  public function listaTrazasAction(Request $request)
  {

    $entityManager = $this->getDoctrine()->getManager();
    $applicationRepository = $entityManager->getRepository('AppBundle:equipo');


    $inventarios = $applicationRepository->findAll();
    $applicationRepository = $entityManager->getRepository('AppBundle:equipo');
    $paginator = $this->get('knp_paginator');
    $pagination = $paginator->paginate(
      $inventarios,
      $request->query->getInt('page', 1),
      10
    );

    //dump($inventarios);die();
    $areas = $entityManager->getRepository('AppBundle:area')->findAll();
    return $this->render(
      'administracion/historial/trazas.html.twig', array('pagination' => $pagination, 'inventarios' => $inventarios, 'areas' => $areas

    ));
  }

}
