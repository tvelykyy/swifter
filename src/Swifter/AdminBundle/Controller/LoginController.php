<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    public function renderLoginPageAction()
    {
        return $this->render('SwifterAdminBundle:Main:login.html.twig');
    }
}
