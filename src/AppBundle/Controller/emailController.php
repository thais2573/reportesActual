<?php
/**
 * Created by PhpStorm.
 * User: Lisandra
 * Date: 08/03/2019
 * Time: 14:13
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
class emailController extends Controller
{
    protected $mailer;

    function __construct(\Swift_Mailer $mailer) {
        $this->mailer = $mailer;
    }


    public function sendMail($email){

        $message = (new \Swift_Message())
            ->setSubject('send mail')
            ->setFrom('xx@yy.com')
            ->setTo($email)
            ->setBody('TEST')
            ->setContentType("text/html");

        $this->mailer->send($message);



        return 1;
    }


}