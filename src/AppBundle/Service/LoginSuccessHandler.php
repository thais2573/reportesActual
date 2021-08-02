<?php
/**
 * Created by PhpStorm.
 * User: talonso
 * Date: 16/10/2018
 * Time: 01:22 PM
 */

namespace AppBundle\Service;


use SensioLabs\Security\SecurityChecker;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    protected $router, $security;

    public function __construct(Router $router, Security $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        // TODO: Implement onAuthenticationSuccess() method.
        # esta es mi ruta por defecto para un usuario
        $url = 'control_reportes';
        if($this->security->isGranted('ROLE_ADMIN')) {
            # y en caso de que el usuario contenta el rol Admin
            # esta es la ruta donde quiero enviarlo
            $url = 'control_reportes';
        }
        $response = new RedirectResponse($this->router->generate($url));

        return $response;
    }

}