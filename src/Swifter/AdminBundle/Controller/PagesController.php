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

    public function getParentBlocksAction($id)
    {
        $page = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Page')
            ->find($id);

        $parent = $page->getParent();
        $jsonParent = $this->serializationService->serializeToJsonByGroup($parent, 'parentBlocks');

        return $this->responseService->generateJsonResponse($jsonParent);
    }

    public function getBlocksAction($id)
    {
        $page = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Page')
            ->find($id);

        $blocks = array();

        $this->appendDeficientBlocks($page->getPageBlocks(), $blocks);
        $currentPage = $page;
        while ($currentPage->getParent())
        {
            $currentPage = $currentPage->getParent();
            $this->appendDeficientBlocks($currentPage->getPageBlocks(), $blocks);
        }
        $json = json_encode($blocks);

        return $this->responseService->generateJsonResponse($json);
    }

    private function appendDeficientBlocks($pageBlocks, & $appendTo)
    {
        foreach ($pageBlocks as $pageBlock)
        {
            if (!isset($appendTo[$pageBlock->getBlock()->getId()]))
            {
                $appendTo[$pageBlock->getBlock()->getId()] = $pageBlock->getContent();
            }
        }
    }


}