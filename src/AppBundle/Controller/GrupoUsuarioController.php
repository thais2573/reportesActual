<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Nomencladores\GrupoUsuario;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Form\GrupoUsuarioType;

/**
 * GrupoUsuario controller.
 *
 * @Route("/nomencladores_grupousuario")
 */
class GrupoUsuarioController extends Controller
{
    /**
     * Lists all GrupoUsuario entities.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/", name="nomencladores_grupousuario_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $grupousuarios = $em->getRepository('AppBundle:Nomencladores\GrupoUsuario')->findAll();

      //  dump($grupousuarios);die();
        return $this->render('nomencladores/grupousuario/index.html.twig', array(
            'entities' => $grupousuarios,
        ));
    }

    /**
     * Creates a new GrupoUsuario entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/new", name="nomencladores_grupousuario_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $grupousuarios = new GrupoUsuario();
        $form = $this->createForm(GrupoUsuarioType::class, $grupousuarios);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($grupousuarios);
            $em->flush();

            return $this->redirectToRoute('nomencladores_grupousuario_show', array('id' => $grupousuarios->getId()));
        }

        return $this->render('nomencladores/grupousuario/new.html.twig', array(
            'entity' => $grupousuarios,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a GrupoUsuario entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/detalles", name="nomencladores_grupousuario_show")
     * @Method("GET")
     */
    public function showAction(GrupoUsuario $grupousuarios)
    {
        $deleteForm = $this->createDeleteForm($grupousuarios);

        return $this->render('nomencladores/grupousuario/show.html.twig', array(
            'entity' => $grupousuarios,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing GrupoUsuario entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/edit", name="nomencladores_grupousuario_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, GrupoUsuario $grupousuarios)
    {
        $deleteForm = $this->createDeleteForm($grupousuarios);
        $editForm = $this->createForm(GrupoUsuarioType::class, $grupousuarios);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($grupousuarios);
            $em->flush();

            return $this->redirectToRoute('nomencladores_grupousuario_show', array('id' => $grupousuarios->getId()));
        }

        return $this->render('nomencladores/grupousuario/edit.html.twig', array(
            'entity' => $grupousuarios,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a GrupoUsuario entity.
     *
     * @Route("/{id}", name="nomencladores_grupousuario_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, GrupoUsuario $grupousuarios)
    {
        $form = $this->createDeleteForm($grupousuarios);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($grupousuarios);
            $em->flush();
        }

        return $this->redirectToRoute('nomencladores_grupousuario_index');
    }

    /**
     * Creates a form to delete a GrupoUsuario entity.
     *
     * @param GrupoUsuario $grupousuarios The Nomencladores\GrupoUsuario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(GrupoUsuario $grupousuarios)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nomencladores_grupousuario_delete', array('id' => $grupousuarios->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
