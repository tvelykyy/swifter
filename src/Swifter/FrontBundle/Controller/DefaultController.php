<?php

namespace Swifter\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($uri)
    {
        $fullUri = $this->leadWithSlash($uri);
        $page = $this->getDoctrine()
            ->getRepository('SwifterFrontBundle:Page')
            ->findOneByUri($fullUri);

        if (!$page) {
            throw $this->createNotFoundException('Page not found.');
        }

        return $this->render('SwifterFrontBundle:Default:index.html.twig', array('name' => $fullUri));
    }

    private function leadWithSlash($uri)
    {
        return '/'.$uri;
    }
}
