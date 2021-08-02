<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Nomencladores\Roles;
use AppBundle\Form\RolesType;

/**
 * Roles controller.
 *
 * @Route("/nomencladores_roles")
 */
class RolesController extends Controller
{
    /**
     * Lists all Roles entities.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/", name="nomencladores_roles_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('AppBundle:Nomencladores\Roles')->findAll();

        return $this->render('nomencladores/roles/index.html.twig', array(
            'entities' => $roles,
        ));
    }

    /**
     * Creates a new Nomencladores\Roles entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/new", name="nomencladores_roles_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $roles = new Roles();
        $form = $this->createForm(RolesType::class, $roles);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($roles);
            $em->flush();

            return $this->redirectToRoute('nomencladores_roles_show', array('id' => $roles->getId()));
        }

        return $this->render('nomencladores/roles/new.html.twig', array(
            'entities' => $roles,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Nomencladores\Roles entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/detalles", name="nomencladores_roles_show")
     * @Method("GET")
     */
    public function showAction(Roles $roles)
    {
        $deleteForm = $this->createDeleteForm($roles);

        return $this->render('nomencladores/roles/show.html.twig', array(
            'entity' => $roles,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Nomencladores\Roles entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}/edit", name="nomencladores_roles_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Roles $roles)
    {
        $deleteForm = $this->createDeleteForm($roles);
        $editForm = $this->createForm(RolesType::class, $roles);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($roles);
            $em->flush();

            return $this->redirectToRoute('nomencladores_roles_show', array('id' => $roles->getId()));
        }

        return $this->render('nomencladores/roles/edit.html.twig', array(
            'entity' => $roles,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Nomencladores\Roles entity.
     *
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/{id}", name="nomencladores_roles_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Roles $roles)
    {
        $form = $this->createDeleteForm($roles);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($roles);
            $em->flush();
        }

        return $this->redirectToRoute('nomencladores_roles_index');
    }

    /**
     * Creates a form to delete a Nomencladores\Roles entity.
     *
     * @param Roles $roles The Nomencladores\Roles entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Roles $roles)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('nomencladores_roles_delete', array('id' => $roles->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
