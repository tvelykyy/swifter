<?php

namespace Swifter\AdminBundle\Controller;

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

        $jsonPages = $this->serializationService->serializeToJsonByGroup($pages, 'details');

        return $this->responseService->generateJsonResponse($jsonPages);
    }

}