<?php

namespace Swifter\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {
        return $this->render('SwifterUserBundle:Main:login.html.twig');
    }
}
