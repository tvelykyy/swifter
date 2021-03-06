<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoginController extends Controller
{
    public function renderLoginAction()
    {
        if( $this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            return $this->redirect($this->generateUrl('admin_ui_landing'));
        }
        return $this->render('SwifterAdminBundle::login.html.twig');
    }
}
