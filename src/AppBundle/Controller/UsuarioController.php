<?php

namespace AppBundle\Controller;

use AppBundle\Form\ChangePassword;
use AppBundle\Form\CuentaType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Administracion\Usuario;
use AppBundle\Form\UsuarioType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Administracion\Usuario controller.
 *
 */
class UsuarioController extends Controller
{
    private $oldpassword;
    private $newpassword;

    /**
     * Lists all Administracion\Usuario entities.
     *
     * @Route("reportes/admin_usuarios/{maxItemPerPage}", name="administracion_usuario_index")
     */
    public function indexAction(Request $request, $maxItemPerPage = 10)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Administracion\Usuario')->findAll();
        $datos = new ArrayCollection();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $entity,
            $request->query->getInt('page', 1),
            $maxItemPerPage
        );
        //  dump($pagination);die();
        return $this->render('administracion/usuario/index.html.twig', array(
            'entities' => $entity, 'pagination' => $pagination
//            'datos_user'=>$datos
        ));
    }

    /**
     * @Route("/reportes/usuario/buscar_usuario/",name="buscar_usuario")
     */
    public function buscarUsuarioAction(Request $request)
    {
        // dump($request);die();
//
        $usuario = $request->get('usuario');

        if ($usuario == '' or $usuario == null) {
            $this->addFlash('alert', 'Usted debe escribir el usuario que desea buscar');
            return $this->redirectToRoute('administracion_usuario_index');
        }
        if ($usuario != null) {
            $entityManager = $this->getDoctrine()->getManager();
            $applicationRepository = $entityManager->getRepository('AppBundle:Administracion\Usuario')->findOneBy(['username' => $usuario]);
            iF ($applicationRepository != null) {
                return $this->render('administracion/usuario/index.html.twig', ['entity' => $applicationRepository]);
            } else {
                $this->addFlash('alert', 'El usuario especificado no existe');

                return $this->redirectToRoute('administracion_usuario_index');
            }
        }
    }
//            dump($applicationRepository);die();
//
//        } else


    /**
     * Creates a new Administracion\Usuario entity.
     * @Route("/{add}/usuario/new", name="administracion_usuario_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $add = $request->get('add');
        $em = $this->getDoctrine()->getManager();
        $entity = new Usuario();
        $form = $this->createForm(UsuarioType::class, $entity);
        $form->handleRequest($request);
        $user = $request->get('usuario');
        if ($form->isSubmitted() && $form->isValid()) {
            $formulario=$form->getData();

            if ($add === 'sist') {
                $usuarios = $request->get('usuarios');

                foreach ($usuarios as $usuario) {

                    $user = $em->getRepository('AppBundle:Administracion\Usuario')->findAllUserIdUms($usuario);

                    $userNew = $em->getRepository('AppBundle:Administracion\Usuario')->findBy(array('idAccount' => (int)$user[0]['id']));
               //     dump($userNew);die();
                    if (empty($userNew)) {
                        // dump('aqui');die();
                        $entity->setIdAccount($user[0]['id']);
                        $entity->setUsername($user[0]['username']);
                        $entity->setCi($user[0]['personal_ID']);
                        $entity->setPassword(strtolower($user[0]['first_name'] . '2019'));
                        $nombre = ucfirst(strtolower($user[0]['first_name']));

                        $apellido = ucfirst(strtolower($user[0]['last_name']));
                       // $entity->setRol($formulario->getRol());
                        $entity->setFirstName($nombre);
                        $entity->setLastName($apellido);
                        $entity->setEmail($user[0]['mail_username']);
                        $entity->setPlainPassword('');
                        $encoder_factory = $this->get('security.password_encoder');
                        $password_encode = $encoder_factory->encodePassword($entity, $entity->getPassword());
                        $entity->setPassword($password_encode);
                        // $entity->setPlainPassword(strtolower($user[0]['username'] . '2019'));
//                        dump($user);
//                        dump($formulario);
//                        dump($entity);
//                        die();

                    }
                    else{
                         $this->addFlash('warning','Este usuario ya existe');
                        return $this->redirectToRoute('administracion_usuario_index');
                    }
                }
            }
             else {
//                dump('hola');
                // $roles = $em->getRepository('AppBundle:Nomencladores\GrupoUsuario')->fin;
            //  dump($user['grupos']);
                $encoder_factory = $this->get('security.password_encoder');
                $password_encode = $encoder_factory->encodePassword($entity, $entity->getPassword());
                $entity->setPassword($password_encode);
                $entity->setFirstName($user['username']);
//                $gruposLista= new ArrayCollection();
//                foreach ($user['grupos'] as $g){
//                    $grupo = $em->getRepository('AppBundle:Nomencladores\GrupoUsuario')->findBy(array('id' => (int)$g[0]));
//                    dump($grupo[0]);
//                    $gruposLista->add($grupo);
//                 }
                //dump($gruposLista);
                 // $entity->setGrupos($gruposLista);
             //   $entity->setRol($user['rol']);
                $entity->setLastName(696969);
                $entity->setEmail(696969);
                $entity->setEstado(1);
                $entity->setPassword(strtolower($user['username'] . '2019'));
                $entity->setIdAccount(696969);
                $entity->setCi(696969);
                 $entity->setPlainPassword('');
                 $entity->setConsecutivoSolicitud(0);
                $encoder_factory = $this->get('security.password_encoder');
                $password_encode = $encoder_factory->encodePassword($entity, $entity->getPassword());
                $entity->setPassword($password_encode);
//                dump($entity);
//                 die();
            }
//            dump($entity);
//            dump($userNew);die();
            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('administracion_usuario_index');
        }

        return $this->render('administracion/usuario/new.html.twig', array(
            'add' => $request->get('add'),
            'entity' => $em->getRepository('AppBundle:Administracion\Usuario')->findAllUserUms(),

            'usuarios' => $em->getRepository('AppBundle:Administracion\Usuario')->findBy(array('estado' => 1)),
            'form' => $form->createView(),
        ));
    }


    /**
     * Finds and displays a Administracion\Usuario entity.
     *
     * @Route("/{id}/detalles", name="administracion_usuario_show")
     * @Method("GET")
     */
    public function showAction(Usuario $entity)
    {
        $deleteForm = $this->createDeleteForm($entity);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:Administracion\Usuario')->findDetallesUms($entity->getIdAccount());

        return $this->render('administracion/usuario/show.html.twig', array(
            'entity' => $user,
            'user' => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Administracion\Usuario entity.
     *
     * @Route("/{id}/edit", name="administracion_usuario_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Usuario $entity)
    {
        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createForm(UsuarioType::class, $entity);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

//      $roleU = array();
//      for ($i = 0; $i < $iMax = count($entity->getGrupos()); $i++) {
//        for ($j = 0; $j < $iMax = count($entity->getGrupos()[$i]->getRoles()); $j++) {
//          $roleU[] = $entity->getGrupos()[$i]->getRoles()[$j];
//        }
//      }
//
//      for ($i = 0; $i < $iMax = count($roleU); $i++) {
//        if(!in_array($roleU[$i], $entity->getRoles()))
//          $entity->addRol($roleU[$i]);
//      }

            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('administracion_usuario_show', array('id' => $entity->getId()));
        }

        return $this->render('administracion/usuario/edit.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Administracion\Usuario entity.
     *
     * @Route("/{id}/cambiar_contrasena", name="administracion_usuario_password")
     * @Method({"GET", "POST"})
     */
    public function changePasswordAction(Request $request, Usuario $entity)
    {
        //$entity = $this->getUser();
        $editForm = $this->createForm(UsuarioType::class, $entity, array('allow_extra_fields' => true));
        $editForm->handleRequest($request);
//        dump($entity);
//        die();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $pass = $request->get('contrasena');
            $encoder_factory = $this->get('security.password_encoder');
            $password_encode = $encoder_factory->encodePassword($entity, $pass);
            $entity->setPassword($password_encode);

            $em->persist($entity);
            $em->flush();

            return $this->redirectToRoute('administracion_usuario_show', array('id' => $entity->getId()));
        }
//        dump($entity);
//        die();
        return $this->render('administracion/usuario/password.html.twig', array(
            'entity' => $entity,
            'edit_form' => $editForm->createView()
        ));
    }

    /**
     * Deletes a Administracion\Usuario entity.
     *
     * @Route("/{id}", name="administracion_usuario_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Usuario $entity)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirectToRoute('administracion_usuario_index');
    }

    /**
     * Creates a form to delete a Administracion\Usuario entity.
     *
     * @param Usuario $entity The Administracion\Usuario entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Usuario $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('administracion_usuario_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Muestra los usuer conectados en ese momento
     *
     * @Route("/reportes/conectados", name="historial_conectados")
     */
    public function conectadosAction()
    {
        $em = $this->getDoctrine()->getManager();
        $conec = $em->getRepository('AppBundle:Administracion\Usuario')->findUserActive();

        return $this->render('administracion/historial/conectados.html.twig', array(
            'entities' => $conec,
        ));
    }

    /**
     * Muestra los usuer conectados en ese momento
     *
     * @Route("/reportes/usuarios2", name="usuariosUMS")
     * @return JsonResponse
     */
    public function usuariosUMSAction()
    {
        $em = $this->getDoctrine()->getManager();
        $conec = $em->getRepository('AppBundle:Administracion\Usuario')->findAllUserUms();
        return new JsonResponse($conec);
    }

    /**
     * @param Request $request
     *
     * @Route("/autocomplete", name="ajax_autocomplete")
     *
     * @return Response
     */
    public function autocompleteAction(Request $request)
    {
        $as = $this->get('tetranz_select2entity.autocomplete_service');
        $result = $as->getAutocompleteResults($request, UsuarioType::class);
        return new JsonResponse($result);
    }

    /**
     * @Route("/cuenta", name="modificar_cuenta")
     */
    public function modificarCuentaAction(Request $request)
    {
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(CuentaType::class, $changePasswordModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser(); //metemos como id la del usuario sacado de su sesion
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $password = $encoder->encodePassword($changePasswordModel->getPassword(), $user->getSalt());
            //  dump($password);
            $user->setPassword($password);
//            dump($request);
//            dump($user);
//            die();
            $em->persist($user);
            $flush = $em->flush();
            // dump($user);
            //die();
            if ($flush === null) {
                $this->addFlash('success', 'El usuario se ha editado correctamente');
                return $this->redirectToRoute("modificar_cuenta"); //redirigimos la pagina si se incluido correctamete
            } else {
                $this->addFlash('warning', 'Error al editar el password');
            }
        } else {
            // dump($form->getErrors());
           // $this->addFlash('warning', 'El password no se ha editado por un error en el formulario.Recuerde que la contraseña debe tener al menos 6 caracteres.');
        }
        return $this->render('administracion/usuario/cambiar_pass.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/change_pass", name="change_pass")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function editPassAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getUser();
        $changePasswordModel = new ChangePassword();
        $form = $this->createForm(CuentaType::class, $changePasswordModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datos = $form->getData();
            //  $nuevo=$datos->getPassword();
            //dump($nuevo);
//            dump($datos);
//            dump($request);die();
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
            $password = $encoder->encodePassword($changePasswordModel->getPassword(), $user->getSalt());
//                dump($password);
//                die();
            // $user->setPassword($password);
            //  $newpassword = $passwordEncoder->encodePassword($user,$nuevo);
            //dump($newpassword);

            $oldpassword = $user->getPassword();
            //dump($oldpassword);
            if ($password == $oldpassword) {
                $this->addFlash('danger', "El nuevo password coincide con el anterior.");
                dump('entre aqui');
                die();
            } else {
                $user->setPassword($newpassword);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                dump('aquii');
            }

            dump($user->getPassword());
            //dump($oldpassword);
            die();
            $this->addFlash('success', 'Su pass ha sido cambiado correctamente');

            # Redirection sur la page de connexion
            return $this->redirectToRoute('change_pass');
        }
        return $this->render(
            'administracion/usuario/cambiar_pass.html.twig',
            array('form' => $form->createView())
        );
    }
    /**
     * @Route("/reportes/usuario/consecutivo",name="cambiar_consecutivo")
     */
    public function cambiarConsecutivoAction(Request $request){

        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm('AppBundle\Form\consecutivoForm');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $nuevoNumero=$request->request->get('app_bundleconsecutivo_form')['consecutivoSolicitud'];
            $this->getUser()->setConsecutivoSolicitud($nuevoNumero);
            $entityManager->persist($this->getUser());
            $entityManager->flush();
            $this->addFlash('success', 'El numero ha sido cambiado correctamente');
            return $this->redirectToRoute('cambiar_consecutivo');
          //dump(  $this->getUser());die();
        }
        return $this->render(
            'administracion/usuario/cambiar_consecutivo.html.twig', array(
            'form' => $form->createView()));
    }

}
