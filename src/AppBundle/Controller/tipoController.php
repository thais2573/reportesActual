<?php

namespace AppBundle\Controller;

use AppBundle\Entity\tipo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class tipoController extends Controller
{
    private $filters = [];

    private $pagination = [];
    /**
     * @Route("/tipo/list", name="tipo_list")
     * @Method("GET")
     * @return Response
     */
    public function listAction()
    {


            $entityManager = $this->getDoctrine()->getManager();
            $applicationRepository = $entityManager->getRepository('AppBundle:tipo')->findAll();


        return $this->render(
            'tipo/list.html.twig',
            [
              'lista'=>$applicationRepository
            ]
        );
    }




    /**
     * @Route("tipo/{id}/remove", name="tipo_remove")
     * @param $ci
     * @return Response
     */
    public function removeAction($id)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $tipodRepository = $entityManager->getRepository('AppBundle:tipo');

        $tipo = $tipodRepository->find($id);


        if ($tipo == null) {
            throw $this->createNotFoundException("Categoría no encontrada");
        }
        $entityManager->remove($tipo);
        $entityManager->flush();
        $this->addFlash('success', 'Categoría eliminada');

        return $this->redirectToRoute('tipo_list');
    }

    /**
     * @Route("tipo/new", name="tipo_new")
     */
    public function newAction(Request $request)
    {
        $tipoForm = $this->createForm('AppBundle\Form\tipoFormType');
        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid())
        {

            $tipo = $tipoForm->getData();

            $entityManager = $this->getDoctrine()->getManager();
            if($tipo->getPrioridad()=='Alta'){
                $tipo->setTiempo('72');

            }
            if($tipo->getPrioridad()=='Media'){
                $tipo->setTiempo('48');

            }
            if($tipo->getPrioridad()=='Baja'){
                $tipo->setTiempo('24');

            }


            $entityManager->persist($tipo);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría Creada Correctamente');
            return $this->redirectToRoute('tipo_list');
        }
        return $this->render('tipo/new.html.twig', ['tipoForm' => $tipoForm->createView()]);

    }

    /**
     * @Route("tipo/{id}/edit", name="tipo_edit")
     */
    public function editAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $tipo = $entityManager->getRepository(tipo::class)->find($id);


        $tipoForm = $this->createForm('AppBundle\Form\tipoFormType', $tipo);
        /**
         *Star "Post only" section
         */
        $tipoForm->handleRequest($request);
        if ($tipoForm->isSubmitted() && $tipoForm->isValid()) {


            $tipo = $tipoForm->getData();
            if($tipo->getPrioridad()=='Alta'){
                $tipo->setTiempo('24');

            }
            if($tipo->getPrioridad()=='Media'){
                $tipo->setTiempo('48');

            }
            if($tipo->getPrioridad()=='Baja'){
                $tipo->setTiempo('72');

            }
            $entityManager->persist($tipo);
            $entityManager->flush();

            $this->addFlash('success', 'Categoría Editada Correctamente');
            return $this->redirectToRoute('tipo_list');
        }
        /**
         * End "Post only" section
         */
        return $this->render('tipo/edit.html.twig', ['tipoForm' => $tipoForm->createView()]);


    }


  /**
   * @Route("/tipo/list", name="lis")
   * @Method("GET")
   * @return Response
   */
  public function list_depAction()
  {


    $entityManager = $this->getDoctrine()->getManager();
    $applicationRepository = $entityManager->getRepository('AppBundle:tipo')->findAll();


    return $this->render(
      'tipo/list.html.twig',
      [
        'lista'=>$applicationRepository
      ]
    );
  }


}
