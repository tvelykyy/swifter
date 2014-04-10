<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class PagesController extends CrudController
{
    public function renderManagePageAction()
    {
        return $this->render('SwifterAdminBundle::pages_list.html.twig', array('title' => 'Pages Management'));
    }

    public function retrievePagesAction()
    {
        $pages = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Page')
            ->findAll();

        $pagesInJson = $this->serializeToJsonByGroup($pages, 'list');

        return $this->generateJsonResponse($pagesInJson);
    }

}