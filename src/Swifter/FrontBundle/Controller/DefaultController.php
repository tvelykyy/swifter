<?php

namespace Swifter\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($route)
    {
        return $this->render('SwifterFrontBundle:Default:index.html.twig', array('name' => $route));
    }
}
