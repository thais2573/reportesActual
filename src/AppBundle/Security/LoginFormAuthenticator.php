<?php
/**
 * Created by PhpStorm.
 * User: LisMar
 * Date: 13/09/2018
 * Time: 1:26
 */

namespace AppBundle\Security;



use AppBundle\Entity\Administracion\Usuario;
use AppBundle\Form\LoginFromType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    private $formFactory;
    private $entityManager;
    private $route;
    private $passwordEncode;
    protected $router, $security;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager,
                                RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder,Security $security)
    {
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->route = $router;
        $this->passwordEncode = $passwordEncoder;
        $this->security = $security;
    }

    protected function getLoginUrl()
    {
        return $this->route->generate('user_login');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {

        $url = 'control_reportes';
        if($this->security->isGranted('ROLE_USER')) {
            $url = 'control_reportes';
        } $url = 'control_reportes';
      if($this->security->isGranted('ROLE_TECNICO')) {
        $url = 'control_reportes';
      }
        elseif($this->security->isGranted('ROLE_ADMIN')) {
            $url = 'control_reportes';
        }
        $response = new RedirectResponse($this->route->generate($url));

        return $response;
        //  }


    }


    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');

        if (!$isLoginSubmit) {
            return;
        }
        $loginForm = $this->formFactory->create(LoginFromType::class);
        $loginForm->handleRequest($request);
        $data = $loginForm->getData();

        $request->getSession()->set(Security::LAST_USERNAME, $data['_username']);

        return $data;
    }


    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];
        return $this->entityManager->getRepository(Usuario::class)
            ->findOneBy(['username' => $username]);

    }


    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];
        if ($this->passwordEncode->isPasswordValid($user, $password)) {
            return true;
        }
        return false;
    }

}