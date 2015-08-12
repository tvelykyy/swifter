<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\ResponseService;
use Swifter\AdminBundle\Service\CrudService;
use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Service\PageBlockService;
use Swifter\CommonBundle\Service\PageService;

class PageController extends CrudController
{
    const PAGE_CLASS = 'Swifter\CommonBundle\Entity\Page';
    const PAGE_CLASS_BUNDLE_NOTATION = 'SwifterCommonBundle:Page';

    private $pageBlockService;
    private $pageService;

    public function __construct(CrudService $crudService, ResponseService $responseService, SerializationService $serializationService,
                                PageBlockService $pageBlockService, PageService $pageService)
    {
        parent::__construct($crudService, $responseService, $serializationService);
        $this->pageBlockService = $pageBlockService;
        $this->pageService = $pageService;
    }

    public function renderPagesAction()
    {
        return $this->render('SwifterAdminBundle::pages_list.html.twig', array('title' => 'Pages Management'));
    }

    public function renderPagesAddAction()
    {
        return $this->render('SwifterAdminBundle::page_form.html.twig', array('title' => 'Pages Form'));
    }

    public function renderPagesEditAction($id)
    {
        $page = $this->pageService->get($id);
        $pageJson = $this->serializationService->serializeToJsonByGroup($page, 'details');

        return $this->render('SwifterAdminBundle::page_form.html.twig', array('title' => 'Pages Form', 'page' => $pageJson));
    }

    public function getPagesAction()
    {
        $pages = $this->pageService->getAll();
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
            $response = $this->crudService->saveAndGenerateResponse($page);
        }

        return $response;
    }

    public function getBlocksAction($id)
    {
        $page = $this->pageService->get($id);

        $this->pageBlockService->mergePageBlocksWithParents($page);
        $json = $this->serializationService->serializeToJsonByGroup($page, 'page-no-parent-template');

        return $this->responseService->generateJsonResponse($json);
    }

    public function getPagesByNameLike($name)
    {
        $pages = $this->pageService->getByNameLike($name);
        $json = $this->serializationService->serializeToJsonByGroup($pages, 'basic');

        return $this->responseService->generateJsonResponse($json);
    }

}