<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class LoginController extends Controller
{
  /**
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   * @Route ("/login" , name="user_login")
   */

  public function loginAction(Request $request) {
    $autheticationUtils = $this->get('security.authentication_utils');
    $error = $autheticationUtils->getLastAuthenticationError();
    $lastUsername = $autheticationUtils->getLastUsername();
    $loginForm = $this->createForm('AppBundle\Form\LoginFromType', ['_username' => $lastUsername]);

    return $this->render('administracion/login/login2.html.twig', ['loginForm' => $loginForm->createView(), 'error' => $error]);

  }

  /**
   * @Route("/logout", name="user_logout")
   */
  public function logoutAction() {
  }

  /**
   *
   * @Route( "/login", name="home" )
   *
   */
  public function indexAction() {
    return $this->render('administracion/login/index.html.twig');
  }

  /**
   *
   * @Route( "/login_check", name="login_check" )
   *
   */
  public function loginCheckAction() {

    return array();
  }
}
