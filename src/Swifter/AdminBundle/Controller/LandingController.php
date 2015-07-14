<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LandingController extends Controller
{
    public function renderLandingAction()
    {
        return $this->render('SwifterAdminBundle::loggedin_skeleton.html.twig');
    }
}
