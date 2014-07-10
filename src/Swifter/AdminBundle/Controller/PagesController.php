<?php

namespace Swifter\AdminBundle\Controller;

class PagesController extends CrudController
{
    const PAGE_CLASS = 'Swifter\CommonBundle\Entity\Page';

    public function renderManagePageAction()
    {
        return $this->render('SwifterAdminBundle::pages_list.html.twig', array('title' => 'Pages Management'));
    }

    public function renderPageFormAction()
    {
        return $this->render('SwifterAdminBundle::page_form.html.twig', array('title' => 'Pages Form'));
    }

    public function retrievePagesAction()
    {
        $pages = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Page')
            ->findAll();

        $jsonPages = $this->serializationService->serializeToJsonByGroup($pages, 'list');

        return $this->responseService->generateJsonResponse($jsonPages);
    }

    public function savePageAction()
    {
        $page = $this->serializationService->deserializeFromJson($this->get('request')->getContent(), static::PAGE_CLASS);

        $errors = $this->validate($page);

        if (count($errors) > 0) {
            $response = $this->responseService->generateErrorsJsonResponse($errors);
        }
        else
        {
            $response = $this->saveAndGenerateResponse($page);
        }

        return $response;
    }

    public function getParentAction($id)
    {
        $page = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Page')
            ->find($id);

        $parent = $page->getParent();
        $jsonParent = $this->serializationService->serializeToJsonByGroup($parent, 'parentBlocks');

        return $this->responseService->generateJsonResponse($jsonParent);
    }

}